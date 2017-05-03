<?php
namespace uccu\Tanime;

class Title{


    
    function __construct( $name ,$change = true){

        

        $this->init($name,$change);
    }

    /* 初始化标题 */
    function init( $name ,$change = true){

        $name = trim( $name );

        $this->rawTitle = $name;

        if($change){
            
            /* 简体转换 */
            $this->SimplifiedTitle = Format::changeToSimplified( $name );
            /* 加强简体转换 */
            $this->SimplifiedTitle = Format::changeToSimplifiedPlus( $this->SimplifiedTitle );

            $name = $this->SimplifiedTitle;
        
        }
        
        $this->splitedTitle = $name;



        /* 过滤出一般TAG */
        $this->tags = Format::completeToTag( $this->splitedTitle );

        /* 删除无用TAG */
        $this->tags = array_merge($this->tags, Format::deleteTag( $this->splitedTitle ) );
echo $this->splitedTitle;
        /* 获取集数 */
        $this->number = Format::getRawNumber( $this->splitedTitle );

        /* 格式化标题 */
        $this->splitedTitle = Format::changeToSplit( $this->splitedTitle );

        
        /* 分割出非一般的标签 */
        $this->exTags = Format::toExTags( $this->splitedTitle );

        /* 如果获取不到集数再次进度更高级的过滤 */
        if(!$this->number)$this->number = Format::getNumberPlus( $this->exTags );

    }













}