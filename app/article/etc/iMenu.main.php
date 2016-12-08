<?php defined('iPHP') OR exit('What are you doing?');?>
[{
    "id": "article",
    "order": "2",
    "caption": "文章",
    "icon": "pencil-square-o",
    "children": [{
        "caption": "文章系统配置",
        "href": "article&do=config",
        "icon": "cog"
    },{
        "-": "-"
    }, {
        "caption": "栏目管理",
        "href": "article_category",
        "icon": "list-alt"
    }, {
        "caption": "添加栏目",
        "href": "article_category&do=add",
        "icon": "edit"
    }, {
        "-": "-"
    }, {
        "caption": "添加文章",
        "href": "article&do=add",
        "icon": "edit"
    }, {
        "caption": "文章管理",
        "href": "article&do=manage",
        "icon": "list-alt"
    }, {
        "caption": "草稿箱",
        "href": "article&do=inbox",
        "icon": "inbox"
    }, {
        "caption": "回收站",
        "href": "article&do=trash",
        "icon": "trash-o"
    }, {
        "-": "-"
    }, {
        "caption": "用户文章管理",
        "href": "article&do=user",
        "icon": "check-circle"
    }, {
        "caption": "审核用户文章",
        "href": "article&do=examine",
        "icon": "minus-circle"
    }, {
        "caption": "淘汰的文章",
        "href": "article&do=off",
        "icon": "times-circle"
    }, {
        "-": "-"
    }, {
        "caption": "文章评论管理",
        "href": "comment&do=article",
        "icon": "comments"
    }]
}]
