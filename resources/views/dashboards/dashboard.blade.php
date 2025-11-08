<x-app-layout :assets="$assets ?? []">
   <div class="container" style="margin-top: 81px;">
      <div class="text-center mb-5">
         <h2 class="fw-bold display-5" style="font-size: 40px;">Welcome to Your Dashboard</h2>
         <p class="text-muted mt-2">View All Violations | Fetch New Data | Account Settings</p>
      </div>

      <div class="row g-4">

         <!-- Recent Violation Fetches Card -->
         <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100 rounded-4 p-3" style="background-color:#fff;">
               <div class="card-body text-center">
                  <!-- Icon -->
                  <div>
                     <img src="{{ asset('images/fetch-icon.png') }}" alt="Recent Violation Icon" width="40" height="40">
                  </div>

                  <!-- Title -->
                  <h5 class="fw-semibold">Recent Violation Fetches</h5>

                  <!-- Description -->
                  <p class="text-muted small">Learn what your violation means and what to do next.</p>

                  <!-- Button -->
                  <a href="{{ route('violation_api.index') }}" class="btn btn-dark px-4 py-2 fw-medium rounded-3">
                     Explore
                  </a>
               </div>
            </div>
         </div>

         <!-- Violation Records Card -->
         <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100 rounded-4 p-3" style="background-color:#fff;">
               <div class="card-body text-center">
                  <!-- Icon -->
                  <div class="mb-3">
                     <img src="{{ asset('images/records-icon.png') }}" alt="Violation Records Icon" width="40" height="40">
                  </div>

                  <!-- Title -->
                  <h5 class="fw-semibold mb-2">Violation Records</h5>

                  <!-- Description -->
                  <p class="text-muted small mb-4">Learn what your violation means and what to do next.</p>

                  <!-- Button -->
                  <a href="{{ route('violation_api.violation_records') }}" class="btn btn-dark px-4 py-2 fw-medium rounded-3">
                     Explore
                  </a>
               </div>
            </div>
         </div>

      </div>

      <style>
         .card {
            transition: all 0.25s ease;
            background-color: #fff;
            height: auto;
            justify-content: center;
            align-items: center;
            display: grid;
         }

         .card:hover {
            transform: translateY(-4px);
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.08);
         }

         .btn-dark {
            background-color: #000;
            border: none;
         }

         .btn-dark:hover {
            background-color: #222;
         }
      </style>

   </div>
</x-app-layout>