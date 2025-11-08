@extends('global.datatable', [
'pageTitle' => $pageTitle,
'auth_user' => $auth_user,
'assets' => $assets,
'headerAction' => $headerAction,
'dataTable' => $dataTable
])


@section('filters')
<div class="row mt-5 mb-3 px-3">
   <!-- <div class="col-12 mb-2">
      <h6><i class="fas fa-calendar-alt me-1"></i> Filter by Issue Date</h6>
   </div>
   <div class="col-md-3">
      <input type="date" id="min_date" class="form-control" placeholder="Start Date">
   </div>
   <div class="col-md-3">
      <input type="date" id="max_date" class="form-control" placeholder="End Date">
   </div> -->
   <div class="col-md-3">
      <!-- <label>Valid Address</label> -->
      <select id="filter_address" class="form-control">
         <option value="">All</option>
         <option value="0">Pending</option>
         <option value="1">Valid</option>
         <option value="2">Invalid</option>
      </select>
   </div>
   <div class="col-md-3">
      <!-- <label>Mail Progress</label> -->
      <select id="filter_mail" class="form-control">
         <option value="">All</option>
         <option value="Editing">Editing</option>
         <option value="Awaiting Production">Awaiting Production</option>
         <option value="In Production">In Production</option>
      </select>
   </div>
   <div class="col-md-2 d-flex justify-center align-center">
      <button id="filterBtn" class="btn btn-primary">Filter</button>
   </div>
</div>
@endsection

@push('scripts')
<script>
   (function() {
      function getTableInstance() {
         if (window.LaravelDataTables && window.LaravelDataTables['dataTable']) {
            return window.LaravelDataTables['dataTable'];
         }
         if ($.fn.dataTable.isDataTable('#dataTable')) {
            return $('#dataTable').DataTable();
         }
         return null;
      }

      function initFilters() {
         const table = getTableInstance();
         if (!table) return setTimeout(initFilters, 100);

         $('#filterBtn').on('click', function() {
            table.ajax.reload();
         });

         $('#dataTable').on('preXhr.dt', function(e, settings, data) {
            data.address_filter = $('#filter_address').val();
            data.mail_filter = $('#filter_mail').val();
            const min = $('#min_date').val();
            const max = $('#max_date').val();
            if (min) data.min_date = min;
            if (max) data.max_date = max;
         });
      }

      document.addEventListener('DOMContentLoaded', initFilters);
   })();

   // ✅ Global functions   

   // $(document).on('click', '.verify-address', function(e) {
   //    showLoader(); 
   //    e.preventDefault();
   //    let id = $(this).data('id');

   //    $.ajax({
   //       url: "",
   //       type: "POST",
   //       data: {
   //             id: id,
   //             _token: "{{ csrf_token() }}"
   //       },
   //       success: function(response) {
   //          Swal.fire({
   //             icon: 'success',
   //             title: 'Success',
   //             text: response.message,
   //             confirmButtonColor: "#3a57e8"
   //          });

   //       },
   //       error: function(xhr, status, error) {
   //          let title = 'Error';
   //          let text = 'Something went wrong';
   //          let icon = 'error';
   //          let confirmColor = "#a50404ff";

   //          switch(xhr.status) {
   //             case 400:
   //                   title = 'Bad Request';
   //                   text = xhr.responseJSON?.message || 'Invalid request';
   //                   icon = 'warning';
   //                   confirmColor = "#f39c12ff";
   //                   break;
   //             case 401:
   //                   title = 'Unauthorized';
   //                   text = xhr.responseJSON?.message || 'You are not authorized';
   //                   icon = 'warning';
   //                   confirmColor = "#f39c12ff";
   //                   break;
   //             case 404:
   //                   title = 'Not Found';
   //                   text = xhr.responseJSON?.message || 'No record found';
   //                   icon = 'info';
   //                   confirmColor = "#0080b3ff";
   //                   break;
   //             case 422:
   //                   title = 'Validation Error';
   //                   text = xhr.responseJSON?.message || 'Invalid data';
   //                   icon = 'warning';
   //                   confirmColor = "#f39c12ff";
   //                   break;
   //             case 500:
   //                   title = 'Server Error';
   //                   text = xhr.responseJSON?.message || 'Internal server error';
   //                   icon = 'error';
   //                   confirmColor = "#a50404ff";
   //                   break;
   //             default:
   //                   title = 'Error';
   //                   text = xhr.responseJSON?.message || error || 'Something went wrong';
   //                   icon = 'error';
   //                   confirmColor = "#a50404ff";
   //          }

   //          Swal.fire({
   //             icon: icon,
   //             title: title,
   //             text: text,
   //             confirmButtonColor: confirmColor
   //          });
   //       },
   //       complete: function() {
   //          hideLoader(); // loader hide
   //          $('#dataTable').DataTable().ajax.reload();
   //       }
   //    });
   // });

   // $(document).on('click', '.send-mail', function(e) {

   //    showLoader(); 
   //    e.preventDefault();
   //    var recordId = $(this).data('id');

   //    $.ajax({
   //       url: "",
   //       type: 'POST',
   //       data: {
   //             record_id: recordId,
   //             _token: '{{ csrf_token() }}'
   //       },
   //       success: function(response) {
   //             if(response.status == 'success'){
   //                alert(response.message);
   //             } else {
   //                alert('Error: ' + response.message);
   //             }
   //       },
   //       error: function(err){
   //             alert('AJAX error');
   //       }
   //    });
   // });
</script>
@endpush