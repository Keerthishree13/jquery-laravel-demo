<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>Laravel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <link rel="stylesheet" text="text/css" href="https://cdn.datatables.net/1.10.25/css/jquery.dataTables.css">
</head>

<body>

<div class="row">
    <div class="col-md-6 offset-4" style="margin-top:200px">
{{--     <h2>Click Here</h2>--}}
        <a class="btn btn-info mb-4" data-bs-toggle="modal" data-bs-target="#exampleModal">Add Category</a>
        <table id="category-table" class="table">
            <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">Name</th>
                <th scope="col">Type</th>
                <th scope="col">Actions</th>
            </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
        <div class="modal fade ajax-modal" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <form id="Form">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h2 class="modal-title " id="modal-title"></h2>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="category_id" id="category_id">
                        <div class="form-group">
                            <label for="">Name</label>
                            <input type="text" name="name" id="name" class="form-control">
                            <span id="nameError" class="text-danger error-messages"></span>
                        </div>
                        <div class="form-group ">
                            <label for="">Type</label>
                            <select name="type" id="type" class="form-control">
                                <option disabled selected>Choose Option</option>
                                <option value="Electronic">Electronic</option>
                                <option value="Furniture">Furniture</option>
                            </select>
                            <span id="typeError" class="text-danger error-messages"></span>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" id="myButton"></button>
                    </div>
                </div>
            </div>
            </form>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.js"></script>
<script>
    $(document).ready(function(){

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

         var table = $('#category-table').DataTable({
            processing: true,
            serverSide: true,

            ajax: "{{ route('categories.index') }}",
            columns: [
                {data: 'id' },
                {data: 'name' },
                {data: 'type' },
                {data: 'action', name: 'action', orderable: false, searchable: false},
            ]
        })

        $('#modal-title').html('Create Category');
        $('#myButton').html('Save Category');
        var form = $('#Form')[0];
        $('#myButton').click(function(){
            $('#myButton').html('Saving...');
            $('#myButton').attr('disabled',true)
            var formData = new FormData(form);

            $('.error-messages').html('');

            $.ajax({
                url: '{{ route("categories.store") }}',
                method: 'POST',
                processData: false,
                contentType: false,
                data: formData,
                success: function(response){
                    table.draw();
                    $('#myButton').attr('disabled',false);
                    $('#myButton').html('Save Category');
                    $('.ajax-modal').modal('hide');
                    if(response){
                        swal('Success!',response.success,'success');
                    }
                },
                error: function(error){
                    $('#myButton').attr('disabled',false);
                    $('#myButton').html('Save Category');
                    if(error){
                        console.log(error.responseJSON.errors.name)
                        $('#nameError').html(error.responseJSON.errors.name);
                        $('#typeError').html(error.responseJSON.errors.type);
                    }
                }
            });
        });

        $('body').on('click', '.editButton', function() {
            var id = $(this).data('id');

            $.ajax({
                url: '{{ url("categories") }}/' + id + '/edit',
                method: 'GET',
                success: function(response) {
                    $('.ajax-modal').modal('show');
                    $('#modal-title').html('Edit Category');
                    $('#myButton').html('Update Category');

                    $('#category_id').val(response.id);
                    $('#name').val(response.name);
                    $('#type').empty();

                    Object.entries(   response.types).forEach(([key, value]) => {
                        $('#type').append('<option selected value="'+value+'">'+value+'</option>');
                    });



                    // $('#type').selectmenu('refresh');
                    //
                    // console.log(response.types);
                    $('#type').val(response.type);
                },
                error: function(error) {
                    console.log(error);
                    alert('An error occurred while fetching the data.');
                }
            });
        });
        $('body').on('click','.deleteButton',function(){
            var id = $(this).data('id');

            if(confirm('Are you sure want to delete it')){
                $.ajax({
                    url: '{{ url("categories/destroy") }}/' + id,
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        table.draw();
                        swal('Success!',response.success,'success');
                    },
                    error: function(error) {
                        console.log(error);
                        alert('An error occurred while fetching the data.');
                    }
                });
            }
        });
        $('#add-category').click(function(){
            $('#modal-title').html('Create Category');
            $('#myButton').html('Save Category');
            $('.error-messages').html('');
        });
    });
</script>
</body>
</html>
