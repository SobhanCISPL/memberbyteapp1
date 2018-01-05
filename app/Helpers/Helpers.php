<?php
if(!function_exists('pr')){
	function pr($param = array(), $continue = true, $label = NULL){
		if (!empty($label))
		{
			echo '<p>-- ' . $label . ' --</p>';
		}

		echo '<pre>';
		print_r($param);
		echo '</pre><br />';

		if (!$continue)
		{
			die('-- code execution discontinued --');
		}
	}
}

if(!function_exists('toSql')){
	function toSql($queryBuilder = null)
	{
		if (($queryBuilder instanceof Illuminate\Database\Query\Builder))
		{
			$sql = $queryBuilder->toSql();
			$aBindings = $queryBuilder->getBindings();
			if (!empty($aBindings))
			{
				foreach ($aBindings as $binding)
				{
					$value = is_numeric($binding) ? $binding : "'" . $binding . "'";
					$sql = preg_replace('/\?/', $value, $sql, 1);
				}
			}
			return $sql;
		}
		return false;
	}
}

if(!function_exists('objToArray')){
	function objToArray($obj)
	{
		if($obj){
			return json_decode(json_encode($obj), true);
		}
		return false;
	}
}