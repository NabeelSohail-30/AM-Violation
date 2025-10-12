@extends('global.datatable', [
    'pageTitle' => $pageTitle,
    'auth_user' => $auth_user,
    'assets' => $assets,
    'headerAction' => $headerAction,
    'dataTable' => $dataTable
])

@push('scripts')
<script>

   // ✅ Global functions   

   $(document).on('click', '.fetch-record', function(e) {
      showLoader(); 
      e.preventDefault();
      let id = $(this).data('id');

      $.ajax({
         url: "{{ route('violation_api.fetch_records') }}",
         type: "POST",
         data: {
               id: id,
               _token: "{{ csrf_token() }}"
         },
         success: function(response) {
               if (response.success) {
                  Swal.fire({
                     icon: 'success',
                     title: 'Success',
                     text: response.message,
                     confirmButtonColor: "#3a57e8"
                  });
                  
               } else {
                  Swal.fire({
                     icon: 'error',
                     title: 'Error',
                     text: response.message,
                     confirmButtonColor: "#a50404ff"
                  });
               }
         },
         error: function(xhr, status, error) {
            // xhr.status me HTTP status code hota hai
            if (xhr.status == 404) {
                  Swal.fire({
                     icon: 'info',
                     title: 'Warning',
                     text: 'No Record Found',
                     confirmButtonColor: "#0080b3ff"
                  });
            } else {
                  Swal.fire({
                     icon: 'error',
                     title: 'Error',
                     text: 'Something went wrong',
                     confirmButtonColor: "#a50404ff"
                  });
            }
         },
         complete: function() {
            hideLoader(); // loader hide
            $('#dataTable').DataTable().ajax.reload();
         }
      });
   });
</script>
@endpush
