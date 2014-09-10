<?php
/**
 * @package iCMS
 * @copyright 2007-2010, iDreamSoft
 * @license http://www.idreamsoft.com iDreamSoft
 * @author coolmoo <idreamsoft@qq.com>
 */
// $GLOBALS['iCONFIG']['user_fs_conf']	= array(
// 	"url"=>"http://s1.ladyband.cn",
// 	"dir"=>"../pic"
// );
defined('iPHP') OR exit('What are you doing?');


//require_once iPHP_APP_DIR.'/user/msg.class.php';
define("USER_CALLBACK_URL", iCMS_API_URL);
define("USER_LOGIN_URL",    iCMS_API_URL.'&do=login');
define("USER_AUTHASH",      '#=(iCMS@'.iPHP_KEY.'@iCMS)=#');

class user {
	public static $userid     = 0;
	public static $nickname   = '';
	public static $cookietime = 0;
	public static $format     = false;
	private static $AUTH      = 'USER_AUTH';

	public static function router($uid,$type,$size=0){
	    switch($type){
	        case 'avatar':return iCMS_FS_URL.get_user_file($uid,$size);break;
	        case 'url':   return iPHP::router(array('/{uid}/',$uid),iCMS_REWRITE);break;
	        case 'urls':
	            return array(
	                'home'      => iPHP::router(array('/{uid}/',$uid),iCMS_REWRITE),
	                'favorite'  => iPHP::router(array('/{uid}/favorite/',$uid),iCMS_REWRITE),
	                'share'     => iPHP::router(array('/{uid}/share/',$uid),iCMS_REWRITE),
	                'follower'  => iPHP::router(array('/{uid}/follower/',$uid),iCMS_REWRITE),
	                'following' => iPHP::router(array('/{uid}/following/',$uid),iCMS_REWRITE),
	            );
	        break;
	    }
	}
	public static function info($uid,$name,$size=0){
		return array(
			'uid'    => $uid,
			'name'   => $name,
			'url'    => self::router($uid,"url"),
			'avatar' => self::router($uid,"avatar",$size?$size:0),
		);
	}
	public static function check($val,$field='username'){
		$uid = iDB::value("SELECT uid FROM `#iCMS@__user` where `$field`='{$val}'");
		return empty($uid)?true:$uid;
	}
	public static function follow($uid=0,$fuid=0){
		$fuid = iDB::row("SELECT `fuid` FROM `#iCMS@__user_follow` where `uid`='{$uid}' and `fuid`='$fuid' limit 1");
		return $fuid?$fuid:false;
	}
	public static function openid($uid=0){
		$pf = array();
		$rs = iDB::all("SELECT `openid`,`platform` FROM `#iCMS@__user_openid` where `uid`='{$uid}'");
		foreach ((array)$rs as $key => $value) {
			$pf[$value['platform']] = $value['openid'];
		}
		return $pf;
	}
	public static function login($v,$pass='',$t='nk'){

		$f = 'username';
		$t =='nk'	&& $f	= 'nickname';
		// $t=='qqoi' 	&& $f	= 'qqopenid';
		// $t=='wboi' 	&& $f	= 'wbopenid';
		// $t=='tboi' 	&& $f	= 'tbopenid';

		$user = iDB::row("SELECT `uid`,`nickname`,`password`,`username` FROM `#iCMS@__user` where `{$f}`='{$v}' and `password`='$pass' AND `status`='1' limit 1");
		if(empty($user)){
			return false;
		}
		self::set_cookie($user->username,$user->password,(array)$user);
		unset($user->password);
		$user->avatar = self::router($user->uid,'avatar');
		$user->url    = self::router($user->uid,'url');
		$user->urls   = self::router($user->uid,'urls');
		return $user;
	}
	public static function set_cache($uid){
		$user	= iDB::row("SELECT * FROM `#iCMS@__user` where `uid`='{$uid}'",ARRAY_A);
		iCache::set('user:'.$user['uid'],$user,0);
	}
	public static function get_cookie($unpw=false) {
		$auth     = authcode(iPHP::get_cookie(self::$AUTH));
		$userid   = authcode(iPHP::get_cookie('userid'));
		$nickname = authcode(iPHP::get_cookie('nickname'));

		list($_userid,$_username,$_password,$_nickname) = explode(USER_AUTHASH,$auth);

		if((int)$userid===(int)$_userid && $nickname===$_nickname){
			self::$userid   = (int)$_userid;
			self::$nickname = $_nickname;
			$U = array('userid'=>self::$userid,'nickname'=>self::$nickname);
			if($unpw){
				$U['username'] = $_username;
				$U['password'] = $_password;
			}
			return $U;
		}
		return false;
	}
	public static function set_cookie($username,$password,$user){
		iPHP::set_cookie(self::$AUTH, authcode((int)$user['uid'].USER_AUTHASH.$username.USER_AUTHASH.$password.USER_AUTHASH.$user['nickname'],'ENCODE'),self::$cookietime);
		iPHP::set_cookie('userid',    authcode($user['uid'],'ENCODE'),self::$cookietime);
		iPHP::set_cookie('nickname',  authcode($user['nickname'],'ENCODE'),self::$cookietime);
	}
	public static function category($cid=0){
		if(empty($cid)) return false;

		$category	= iDB::row("SELECT * FROM `#iCMS@__user_category` where `cid`='".(int)$cid."' limit 1");
		return (array)$category;
	}
	public static function get($uid=0,$unpass=true){
		if(empty($uid)) return false;

		$user         = iDB::row("SELECT * FROM `#iCMS@__user` where `uid`='".(int)$uid."' AND `status`='1' limit 1");
		$user->gender = $user->gender?'male':'female';
		$user->avatar = self::router($user->uid,'avatar');
		$user->url    = self::router($user->uid,'url');
		$user->urls   = self::router($user->uid,'urls');
	   	if($unpass) unset($user->password);
	   	return $user;
	}
    public static function data(){
        $data = iDB::row("SELECT * FROM `#iCMS@__user_data` where `uid`='".user::$userid."' limit 1;");
        //iDB::debug(1);
        if($data){
            if($data->coverpic){
                $data->coverpic = iFS::fp($data->coverpic,'+http');
            }else{
                $data->coverpic = iCMS_PUBLIC_URL.iCMS::$config['user']['coverpic'];
            }
            $data->enterprise&& $data->enterprise = unserialize($data->enterprise);
        }
        return $data;
    }
	public static function status($url=null,$st=null) {
		$status = false;
		$auth   = user::get_cookie(true);

		if($auth){
			$user = self::get($auth['userid'],false);
			if($auth['username']==$user->username && $auth['password']==$user->password){
				$status = true;
			}
			unset($user->password);
		}
		unset($auth);

		if($status){
			if($url && $st=="login"){
				if(self::$format=='json'){
					return iPHP::code(1,0,$url,'json');
				}
				iPHP::gotourl($url);
			}
			return $user;
		}else{
			if($url && $st=="nologin"){
				if(self::$format=='json'){
					return iPHP::code(0,0,$url,'json');
				}
				iPHP::gotourl($url);
			}
			return false;
		}
	}
	public static function logout(){
		iPHP::set_cookie(self::$AUTH, '',-31536000);
		iPHP::set_cookie('userid', '',-31536000);
		iPHP::set_cookie('nickname', '',-31536000);
		iPHP::set_cookie('seccode', '',-31536000);
	}
}