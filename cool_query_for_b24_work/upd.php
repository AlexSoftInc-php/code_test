<?php 
include_once 'class/db.class.php';

$db = DB::run();

$last_id = $_POST['last_id'];
$new_id  = $_POST['new_id']; // используется в качестве current id data

// DATA - last_id = 4952, new_id = 7486

$query = $db->prepare("UPDATE `b_sale_order` SET `USER_ID` = :new_id WHERE `USER_ID` = :last_id");

$query->execute(array(
	':new_id'  => $new_id,
	':last_id' => $last_id
));


if($query)
{
	$q2 = $db->prepare("SELECT `CURRENT_BUDGET` FROM `b_sale_user_account` WHERE `USER_ID` = :last_id_2");
	
	$q2->execute(array(
		':last_id_2' => $last_id
	));

	$q2s = $q2->fetch(PDO::FETCH_OBJ);

	if($q2s->CURRENT_BUDGET > 0)
	{
		$q3 = $db->prepare("SELECT `CURRENT_BUDGET` FROM `b_sale_user_account`  WHERE `USER_ID` = :new_id3");
		$q3->execute(array(
			':new_id3' => $new_id
		));

		$q3s = $q3->fetch(PDO::FETCH_OBJ);

		if($q3s->CURRENT_BUDGET > 0)
		{
			$q4 = $db->prepare("UPDATE `b_sale_user_account` SET `CURRENT_BUDGET` = `CURRENT_BUDGET` + :returnSumma WHERE `USER_ID` = :newid4");
			
			$q4->execute(array(
				':returnSumma' => $q2s->CURRENT_BUDGET,
				':newid4'      => $new_id
			));

			if($q4) 
			{
				// Тут пишем дальше
				$q5 = $db->prepare("UPDATE `b_sale_user_account` SET `CURRENT_BUDGET` = `` WHERE `USER_ID` = :last_id_33");

				$q5->execute(array(
					':last_id_33' => $last_id
				));

				if($q5)
				{
					$q6 = $db->prepare('UPDATE `b_user` SET `ACTIVE` = "N" WHERE `ID` = :last_id_4');

					$q6->execute(array(
						':last_id_4' => $last_id
					));

					if($q6)
					{
						$q7 = $db->prepare
					}
					else
					{
						echo '6 query';
					}
				}
				else
				{
					echo '5 qury';
				}
			}
			else
			{
				echo 'Это если 4 запрос не отработал';

			}
		}
		else
		{
			// 3 query not result 
			DB::insert('b_sale_user_account', array(
				'USER_ID'        => $new_id,
				'TIMESTAMP_X'    => date('Y-m-d H:i:s'),
				'CURRENT_BUDGET' => $q2s->CURRENT_BUDGET,
				'CURRENCY'		 => 'RUB',
				'LOCKED'		 => 'N',
				'DATE_LOCKED'    => 'NULL',
				'NOTES'			 => 'NULL'
			));
		}
	} 
	else
	{
		echo '2 query';
	}

}
else
{
	echo 'BAD';
}
