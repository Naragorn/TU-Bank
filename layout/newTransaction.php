<form action="newTransaction.php" method="post">




	<h2>New Transaction</h2>
	<table>

<tr>
			<td>TAN:</td>
			<td><textarea type="text" name="tan" style="width:100%; height:90px;"><?php
				if (isset ( $_POST ["tan"] )) {
					echo  htmlspecialchars($_POST ["tan"], ENT_QUOTES, 'UTF-8');
				}
				?></textarea></td>
		</tr>
<tr>
			<td></td>
			<td><input type="submit" name="submit" value="Make Transaction" /></td>
		</tr>

	
<tr><td colspan="2">
<div style="padding:32px 0;">The following fields are only needed when not using a Smartcard Simulator:</div></td></tr>
		<tr>
			<td>Recipient User Name:</td>
			<td><input type="text" name="recipient"
				value="<?php
				if (isset ( $_POST ["recipient"] )) {
					echo htmlspecialchars($_POST ["recipient"], ENT_QUOTES, 'UTF-8');
				}else if (isset ( $_GET ["recipient"] )) {
					echo htmlspecialchars($_GET ["recipient"], ENT_QUOTES, 'UTF-8');

				}
				?>" /></td>
		</tr>
		<tr>
			<td>Amount:</td>
			<td><input type="text" name="amount"
				value="<?php
				if (isset ( $_POST ["amount"] )) {
					echo $_POST ["amount"];
				}
				?>" /></td>
		</tr>
		
		<tr>
			<td></td>
			<td><input type="submit" name="submit" value="Make Transaction" /></td>
		</tr>
	</table>
	<a href="upload.php">Or upload a file</a>
</form>
