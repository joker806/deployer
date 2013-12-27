<h4>Subversion info</h4>

<table>
	<tr>
		<th>Repository URL</th>
		<td><?php echo $data['repositoryUrl'] ?></td>
	</tr>
	<tr>
		<th>Revision</th>
		<td><?php echo $data['revision'] ?></td>
	</tr>
	<tr>
		<th>Last change</th>
		<td><?php echo $data['lastChange'] ?></td>
	</tr>
</table>

<pre><?php echo $data['status'] ?></pre>
