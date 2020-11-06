<?php
	require '../connect.php';
	
	function getHistory($id)
	{
		require '../connect.php';
		
		$query = mysqli_query($con,"SELECT * FROM sale_edits WHERE sale_id='" . $id . "' ORDER BY edited_on DESC") or die(mysqli_error($con));
		if(mysqli_num_rows($query)>0)
		{
			foreach($query as $history)
			{
				$historyList[] = $history;
			}
			return $historyList;
		}
		else
		{
			return null;			
		}
	}