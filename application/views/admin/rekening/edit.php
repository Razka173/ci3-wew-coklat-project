<?php
// Notifikasi error
echo validation_errors('<div class="alert alert-warning">','</div>');

// Form open
echo form_open(base_url('admin/rekening/edit/'.$rekening->id_rekening),' class="form-horizontal"');
?>

<div class="form-horizontal">
	<label class="col-md-2 control-label" for="nama_bank">Nama Bank</label>
	<div class="col-md-5">
		<input type="text" name="nama_bank" id="nama_bank" class="form-control" placeholder="Nama Bank" value="<?php echo $rekening->nama_bank ?>" required>
	</div>
</div>
<hr>

<div class="form-horizontal">
	<label class="col-md-2 control-label" for="nomor_rekening">Nomor Rekening</label>
	<div class="col-md-5">
		<input type="number" name="nomor_rekening" id="nomor_rekening" class="form-control" placeholder="Nomor Rekening" value="<?php echo $rekening->nomor_rekening ?>" required>
	</div>
</div>
<hr>

<div class="form-horizontal">
	<label class="col-md-2 control-label" for="nama_pemilik">Nama Pemilik Rekening</label>
	<div class="col-md-5">
		<input type="text" name="nama_pemilik" id="nama_pemilik" class="form-control" placeholder="Nama Pemilik Rekening" value="<?php echo $rekening->nama_pemilik ?>" required>
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