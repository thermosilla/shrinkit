<?php
if ( ! defined('BASEPATH')) exit('Acceso Denegado');

/**
*Manejo de bases de datos MySQL
*@author Tomás Hermosilla
*@version 2.0
*@licence GNU/GPLv2
*/


include_once('conf.inc.php');

    /**
    *Clase para manejar consultas a bases de datos MySQL
    *@package MySQL
    */
    class MySQL
    {
        ###### Variables de uso interno
        var $link;
        var $result;
        var $q;

        ###### Variables para crear la consulta SQL
        var $q_select   = array();
        var $q_distinct = FALSE;
        var $q_from     = array();
        var $q_where    = array();
        var $q_groupby  = array();
        var $q_orderby  = array();
        var $q_order    = "ASC";
        var $q_limit    = FALSE;

        ###### Funciones de Conexión

        private function connect()
        {
            return $this->link = mysql_connect(db_host, db_user, db_pass);
        }

        private function close()
        {
            return mysql_close($this->link);
        }

        ###### Funciones para creación de consulta SQL

        public function select($select = '*')
        {
            if(is_string($select))
            {
                $select = explode(',', $select);
            }

            foreach ($select as $val)
            {
                $val = trim($val);

                if($val != '')
                {
                    $this->q_select[] = $val;
                }
            }
        }

        public function from($from)
        {
            if(is_string($from))
            {
                $from = explode(',', $from);
            }

            foreach ($from as $val)
            {
                $val = trim($val);

                if($val != '')
                {
                    $this->q_from[]=db_database.".".$val;
                }
            }
        }

        /**
        *Agrega sentencias WHERE a consulta
        *@param mixed $key Arreglo con los valores a agregar o string identificando el campo
        *@param string $val Valor del campo, se incluyen los operadores lógicos ejs: '= 20'; '!="hola" '; 'like "foo"'
        */
        public function where($key, $val = NULL, $type = 'AND')
        {
            if(!is_array($key))
            {
                $key = array($key => $val);
            }

            $type = $type." ";

            foreach ($key as $k => $val)
            {
                $prefix = (count($this->q_where)==0)?'':$type;
                $this->q_where[]= $prefix.$k." ".$val;
            }
        }

        public function groupby($groupby)
        {
            if(is_string($groupby))
            {
                $groupby = explode(',', $groupby);
            }

            foreach ($groupby as $val)
            {
                $val = trim($val);

                if($val != '')
                {
                    $this->q_groupby[] = $val;
                }
            }
        }

        public function orderby($orderby, $order="ASC")
        {
            if(is_string($orderby))
            {
                $orderby = explode(',', $orderby);
            }

            $this->q_order = $order;

            foreach ($orderby as $val)
            {
                $val = trim($val);

                if($val != '')
                {
                    $this->q_orderby[] = $val;
                }
            }
        }

        public function setQuery()
        {
            $s_select   = "SELECT %s";
            $s_from     = " FROM %s";
            $s_where    = " WHERE %s";
            $s_groupby  = " GROUP BY %s";
            $s_orderby  = " ORDER BY %s ".$this->q_order;
            $s_limit    = " LIMIT %d";

            $select_elems = (count($this->q_select)==0)?array("*"):$this->q_select;

            $this->q  = sprintf($s_select, implode(", ",$select_elems));
            $this->q .= sprintf($s_from, implode(", ", $this->q_from));

            if(count($this->q_where)!=0)
            {
                $this->q .= sprintf($s_where, implode(" ", $this->q_where));
            }

            if(count($this->q_groupby)!=0)
            {
                $this->q .= sprintf($s_groupby, implode(", ", $this->q_groupby));
            }

            if(count($this->q_orderby)!=0)
            {
                $this->q .= sprintf($s_orderby, implode(", ", $this->q_orderby));
            }

            if($this->q_limit)
            {
                $this->q .= sprintf($s_limit, $this->q_limit);
            }

            return $this->q;
        }

        /**
        *Permite ejecutar consultas arbitrarias
        *@param string $query String con consulta SQL
        */
        public function query($query)
        {
            $this->q = $query;
        }

        ###### Funciones para obtención de resultados

        public function get($table, $limit=FALSE)
        {
            $this->from($table);
            $this->q_limit = $limit;

            $query = $this->setQuery();
            $link = $this->connect();

            $this->result = mysql_query($query, $link);

            $this->close($link);
        }

        public function result()
        {
            $data = array();

            while($data[] = mysql_fetch_assoc($this->result));

            mysql_free_result($this->result);

            foreach ($data as $key => $value)
            {
                if($value){
                    $recordset[$key]=$value;
                }
            }

            $this->q_select   = array();
            $this->q_distinct = FALSE;
            $this->q_from     = array();
            $this->q_where    = array();
            $this->q_groupby  = array();
            $this->q_orderby  = array();
            $this->q_order    = "ASC";
            $this->q_limit    = FALSE;

            return $recordset;
        }

        public function row()
        {

        }

    }
?>

