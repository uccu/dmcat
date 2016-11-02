<?php

namespace Lib\Tool;


interface Sql{


    function select();//选择字段


    function find();//获取一条记录


    function where();//条件


    function orWhere();//条件


    function count();//获取记录的数目


    function get();//获取所有记录


    function order();//添加排序


    function from();//查询表


    function group();


    function join();
    

    function sql();//输出sql语句


    function exceptSelect();


    function on();
    


}