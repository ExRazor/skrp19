<div id="modal-member-teacher" class="modal fade effect-slide-in-right">
    <form method="POST" enctype="multipart/form-data" data-parsley-validate>
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content bd-0 tx-14 modal-form">
                <div class="modal-header pd-y-20 pd-x-25">
                    <h6 class="tx-16 mg-b-0 tx-uppercase tx-inverse tx-bold"><span class="title-action"></span> Anggota Dosen Penelitian</h6>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body pd-20">
                    @include('layouts.alert')
                    <div class="form-group row mg-t-20">
                        <label class="col-sm-3 form-control-label"><span class="tx-danger">*</span> Asal Dosen:</label>
                        <div class="col-sm-8">
                            <div class="row radio">
                                <input type="hidden" name="penelitian_id" value={{encrypt($data->id)}}>
                                <div class="col-lg-5 mg-t-5">
                                    <label class="rdiobox">
                                        <input name="asal_peneliti" type="radio" value="Jurusan" required>
                                        <span>Dosen Jurusan</span>
                                    </label>
                                </div>
                                <div class="col-lg-5 mg-t-5">
                                    <label class="rdiobox">
                                        <input name="asal_peneliti" type="radio" value="Luar" required>
                                        <span>Dosen Luar</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row mg-t-20 tipe-non-lainnya" style="display:none;">
                        <label class="col-sm-3 form-control-label"><span class="tx-danger">*</span> NIDN:</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" name="peneliti_nidn">
                        </div>
                    </div>
                    <div class="form-group row mg-t-20 tipe-lainnya" style="display:none;">
                        <label class="col-sm-3 form-control-label"><span class="tx-danger">*</span> Nama Dosen:</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" name="peneliti_nama" placeholder="Tuliskan nama dosen">
                        </div>
                    </div>
                    <div class="form-group row mg-t-20 tipe-lainnya" style="display:none;">
                        <label class="col-sm-3 form-control-label"><span class="tx-danger">*</span> Asal Dosen:</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" name="peneliti_asal" placeholder="Tuliskan asal dosen">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary tx-11 tx-uppercase pd-y-12 pd-x-25 tx-mont tx-medium btn-save" value="post" data-dest="{{route('research.teacher.store')}}">
                        Simpan
                    </button>
                    <button type="button" class="btn btn-secondary tx-11 tx-uppercase pd-y-12 pd-x-25 tx-mont tx-medium" data-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div><!-- modal-dialog -->
    </form>
</div><!-- modal -->
@push('custom-js')
<script>
    $('#modal-member-teacher').on('change', 'input[name=asal_peneliti]', function() {
        var val = $(this).val();

        var cont = $('#modal-member-teacher');
        var lainnya     = cont.find('.tipe-lainnya');
        var nonlainnya  = cont.find('.tipe-non-lainnya');

        if(val=='Luar') {
            nonlainnya.hide();
            nonlainnya.find('input').prop('disabled',true);
            nonlainnya.find('input').prop('required',false);

            lainnya.show();
            lainnya.find('input').prop('disabled',false);
            lainnya.find('input').prop('required',true);
        } else if (val=='') {
            nonlainnya.hide();
            lainnya.hide();
            nonlainnya.find('input').prop('disabled',true);
            nonlainnya.find('input').prop('required',false);
            lainnya.find('input').prop('disabled',true);
            lainnya.find('input').prop('required',false);
        } else {
            lainnya.hide();
            lainnya.find('input').prop('disabled',true);
            lainnya.find('input').prop('required',false);

            nonlainnya.show();
            nonlainnya.find('input').prop('disabled',false);
            nonlainnya.find('input').prop('required',true);
        }
    });

    $('#modal-publication-author').on('hidden.bs.modal', function () {
        $(this).find('form').trigger('reset');

        var cont           = $(this);
        cont.find('.tipe-lainnya').hide();
        cont.find('.tipe-non-lainnya').hide();
    })
</script>
@endpush
