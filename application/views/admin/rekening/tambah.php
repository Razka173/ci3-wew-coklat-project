<?php
// Notifikasi error
echo validation_errors('<div class="alert alert-warning">','</div>');

// Form open
echo form_open(base_url('admin/rekening/tambah'),' class="form-horizontal"');
?>

<div class="form-horizontal">
	<label class="col-md-2 control-label" for="nama_bank">Nama Bank</label>
	<div class="col-md-5">
		<input type="text" name="nama_bank" id="nama_bank" class="form-control" placeholder="Nama bank" value="<?php echo set_value('nama_bank') ?>" required>
	</div>
</div>
<hr>

<div class="form-horizontal">
	<label class="col-md-2 control-label" for="nomor_rekening">Nomor Rekening</label>
	<div class="col-md-5">
		<input type="number" name="nomor_rekening" id="nomor_rekening" class="form-control" placeholder="Nomor rekening" value="<?php echo set_value('nomor_rekening') ?>" required>
	</div>
</div>
<hr>

<div class="form-horizontal">
	<label class="col-md-4 control-label" for="nama_pemilik">Nama Pemilik Rekening (atas nama)</label>
	<div class="col-md-5">
		<input type="text" name="nama_pemilik" id="nama_pemilik" class="form-control" placeholder="Nama pemilik rekening" value="<?php echo set_value('nama_pemilik') ?>" required>
	</div>
</div>
<hr>

<div class="form-inline">
	<label class="col-md-2 control-label"></label>
	<div class="col-md-5">
		<button class="btn btn-success btn-lg" name="submit" type="submit">
			<i class="fa fa-save"></i> Simpan
		</button>
		<button class="btn btn-info btn-lg" name="reset" type="reset">
			<i class="fa fa-times"></i> Reset
		</button>
	</div>
</div>

<?php echo form_close(); ?>