<form action="editBalance.php" method="post">
	<h2>Edit Balance</h2>
	<table>
		<tr>
			<td>User:</td>
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
				else if (isset ( $_GET ["amount"] )) {
					echo htmlspecialchars($_GET ["amount"], ENT_QUOTES, 'UTF-8');

				}
				?>" /></td>
		</tr>
		<tr>
			<td></td>
			<td><input type="submit" name="submit" value="Save Changes" /></td>
		</tr>
	</table>
</form>
