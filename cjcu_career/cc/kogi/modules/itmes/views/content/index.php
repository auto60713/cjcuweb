<div class="box create rounded">

	<a class="button good" href="<?php echo site_url(SITE_AREA .'/content/itmes/create'); ?>">
		<?php echo lang('itmes_create_new_button'); ?>
	</a>

	<h3><?php echo lang('itmes_create_new'); ?></h3>

	<p><?php echo lang('itmes_edit_text'); ?></p>

</div>

<br />

<?php if (isset($records) && is_array($records) && count($records)) : ?>
				
	<h2>可申請的項目一覽</h2>
	<table>
		<thead>
			<tr>
			
		<th>名稱</th>
		<th>售價</th>
		<th>製作工時</th>
		<th>注意事項</th>
		<th>備註</th>
		
			<th><?php echo lang('itmes_actions'); ?></th>
			</tr>
		</thead>
		<tbody>
		
		<?php foreach ($records as $record) : ?>
			<tr>
				
				<td><?php echo $record->name?></td>
				<td><?php echo $record->price?></td>
				<td><?php echo $record->working_day?></td>
				<td><?php echo $record->notice?></td>
				<td><?php echo $record->remark?></td>
				<td><?php echo anchor(SITE_AREA .'/content/itmes/edit/'. $record->id, lang('itmes_edit'), '') ?></td>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>
<?php endif; ?>