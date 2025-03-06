<?php
class Stack
{
	//tablica z elementami stosu
	private $_array = array();
	//liczba elementow stosu (jednoczesnie wskaznik na wierzcholek stosu)
	private $_top = 0;
	
	//wloz nowy element na stos
	public function push($x)
	{
		$this->_array[$this->_top] = $x;
		$this->_top ++;
	}
	
	//pobierz element ze stosu
	public function pop()
	{
		if (!$this->isEmpty())
		{
			$x = $this->_arrray[$this->_top-1];
			$this->_top --;
			return $x;
		}
		else
			return NULL;
	}
	
	//zwraca element na wierzcholku stosu (nie zdejmuje go)
	public function top()
	{
		if  (!$this->isEmpty())
			return $this->_array[$this->_top-1];
		else
			return NULL;
	}
	
	//zwraca liczbe elementow stosu
	public function size()
	{
		return count($this->_array);
	}
	
	//zwraca true jezeli stos jest pusty, false w przeciwnym wypadku
	public function isEmpty()
	{
		return $this->_top == 0;
	}
	
	//czysci stos
	public function clear()
	{
		while (!$this->isEmpty())
			$this->pop();
	}
}
	
?>