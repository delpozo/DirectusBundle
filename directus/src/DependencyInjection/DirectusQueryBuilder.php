<?php


namespace Sba\DirectusBundle\DependencyInjection;


use http\Exception\InvalidArgumentException;

/**
 * Class DirectusQueryBuilder
 * @package Sba\DirectusSymfonyBundle\DependencyInjection
 */
class DirectusQueryBuilder
{

    /**
     * @var array
     */
    private $_directusQueryParts = [
        'limit'    => [],
        'fields'     => [],
        'filtres'   => [],
        'groupBy' => [],
        'orderBy' => [],
        'search'=>[],
        'page'=>[],
        'meta'=>[],
        'deep'=>[],
        'export'=>[],
        'aggregate'=>[]
    ];

    /**
     * @param int $limit
     * @return $this
     */
    public function limit(int $limit)
{     if(!$limit&& is_integer($limit))
    throw new InvalidArgumentException(
        " $limit is null or type is not a integer "
    );

         $this->_directusQueryParts['limit']=$limit;
     return $this;
}
    /**
     *
     * @param string $searchString
     * @return array|bool
     */
    public function search($searchString) : bool
    {   if(!$searchString || is_string($searchString))
        throw new InvalidArgumentException(
            " $searchString is null or type is not a string "
        );
        $this->_directusQueryParts['search']=$searchString;
        return true;
    }
    /**
     * Skip the first n items in the response. Can be used for pagination.
     * @param int $limit
     * @return $this
     */
    public function offset(int $offset)
    {     if(!$offset&& is_integer($offset))
        throw new InvalidArgumentException(
            "$offset is null or type is not a integer "
        );

            $this->_directusQueryParts['offset']=$offset;
        return $this;
    }

    /**
     * @param int $page
     * @return $this
     */
    public function page(int $page) :void
    {     if(!$page&& is_integer($page))
        throw new InvalidArgumentException(
            " $page is null or type is not a integer "
        );

            $this->_directusQueryParts['page']=$page;

    }
    /**
     * @param string $fields
     * @return void
     */
    public function fileds($fields) : void
    {   if(!$fields || is_string($fields))
        throw new InvalidArgumentException(
            " fields is null or type  not a string "
        );
            $this->setField($fields);

    }

    /**
     * @param string $meta
     * @return void
     */
    public function meta($meta) : bool
    {   if(!$meta || is_string($meta))
        throw new InvalidArgumentException(
            " $meta is null or type is not a string "
        );
        $this->_directusQueryParts['meta']=$meta;
        return true;
    }
    /**
     * Returns the total item count of the collection you're querying.
     * @param
     * @return void
     */
    public function totalCount() :void
    {
        $this->_directusQueryParts['meta']='total_count';
    }
    /**
     * @param $collection
     * @return int
     */
    public function groupBy( string $data) :void
    {   if(!$data)
        throw new InvalidArgumentException(
            " $data is null "
        );
        $this->setGroupBy($data);

    }


    public function orderBy( string $data) :void
    {   if(!$data)
        throw new InvalidArgumentException(
            " $data is null "
        );
        $this->_directusQueryParts['orderBy']='orderBy='. $data;

    }
    public function addOrderBy( string $data) :void
    {   if(!$data)
        throw new InvalidArgumentException(
            " $data is null "
        );
        $this->_directusQueryParts['orderBy']['orderBy=']. $data;
        array_push($this->_directusQueryParts['orderBy']['orderBy='],$data);
    }


    /**
     * @param string $fields
     */
    private function setField(string $fields): void
    {
        if ( !is_null($fields))
        array_push($this->_directusQueryParts['fields'],'fields[]='.$fields);


    }

    /**
     * @param $field
     * @return string
     */
    private function setGroupBy( string $field): string
    {

        array_push($this->_directusQueryParts['groupBy'],'groupBy[]='.$field);

    }

    /**
     * @param $field
     * @return string
     */
    public function addGroupBy(string $field): string
    {

        $this->setGroupBy($field);

    }

    /**
     * @param string $timestamp
     * @param string $field
     * @param integer $value
     * @return string
     */
    private function getTimestamp (string $timestamp , string $field , int $value ): string
    {
        $timestampArgs = ['year' , 'month' , 'week' , 'day' , 'weekday' , 'hour' , 'minute' , 'second'];

        if (!in_array($timestamp, $timestampArgs, true)) {
              throw new InvalidArgumentException(
                " unknown " . $timestamp . "  the authorized calls are:
                'year', 'month', 'week', 'day', 'weekday', 'hour','minute','second'
                "
            );
        }

        return $timestamp . '('.$field . ')';

    }

    /**
     * return groupBy with a TimesTamp exp: ?groupBy[]=year(publish_date)
     * @param $field
     * @param $timestamp
     * @param $value
     * @return string
     */
    private function groupByTimestamp($timestamp, $field, $value): string
    {

        array_push($this->_directusQueryParts['groupBy'],$this->getTimestamp($timestamp,$field,$value));

    }


    /**
     * return filtre with a TimesTamp exp: ?filtre=year(publish_date)
     * @param string $timestamp
     * @param string $field
     * @param integer $value
     * @return  void
     *
     */
    public function filtreTimestamp (string $timestamp, string $field,int $value ): void
    {
        $this->setField($this->getTimestamp($timestamp,$field,$value));
    }


    /**
     *
     * @return array
     */
    public function getDirectusQuery(): array
    {
        $query=[];
        foreach ($this->_directusQueryParts as $key =>$data)
        {
            if ($data) array_push($query,$data);
        }
        return $query;
    }

    /**
     * Deep allows you to set any of the other query parameters on a nested relational dataset.
     * @param string $relationalDataset
     * @param string $field
     * @param string $value
     */
    public function deep(string $relationalDataset , $field , $value)
    {
        array_push($this->_directusQueryParts['deep'],'[' . $relationalDataset .'][_filter][' . $field.'][_eq]=' . $value);
    }
    
    public function aggregate( string $aggregate , string $field)
    {
        $aggregates = ['count', 'countDistinct', 'sum', 'sumDistinct', 'avg', 'avgDistinct','min','max'];

        if (!in_array($aggregate, $aggregates, true)) {
            throw new InvalidArgumentException(
                " unknown " . $aggregate . " the authorized aggregates are:
                'count', 'countDistinct', 'sum', 'sumDistinct', 'avg', 'avgDistinct','min','max' "

            );
        }

        return 'aggregate('.$aggregate . ')=' . $field;
    }

    public function export( string $format ) :void
    {
        $formats = ['csv', 'json', 'xml'];

        if (!in_array($format, $formats, true)) {
            throw new InvalidArgumentException(
                " unknown " . $format . " the authorized format are:
                'csv', 'json', 'xml' "

            );
        }

        $this->_directusQueryParts['export']=$format;
    }

}