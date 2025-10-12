<x-guest-layout>
   <section class="login-content">
      <div class="row m-0 vh-100">
         
         <!-- Left Side (Form Section) -->
         <div class="col-md-6 d-flex flex-column justify-content-center align-items-center bg-white">
            
            <!-- Logo -->
            <div class="text-center mb-4">
               <img src="{{ asset('images/logo.png') }}" alt="AM Logo" style="width: 80px; height: 80px;">
               <h3 class="mt-3 mb-0 fw-semibold">AM Expediting Drafting & Design Works LLC</h3>
            </div>

            <!-- Login Box -->
            <div class="" style="width: 320px;">
               <h5 class="text-center mb-2">Sign In</h5>
               <p class="text-center text-muted mb-4">Login to stay connected.</p>

               <x-auth-session-status class="mb-3" :status="session('status')" />
               <x-auth-validation-errors class="mb-3" :errors="$errors" />

               <form method="POST" action="{{ route('login') }}">
                  @csrf
                  
                  <div class="form-group mb-3">
                     <label for="email" class="form-label small">Email</label>
                     <input type="email" name="email" id="email" 
                        value="{{ old('email') }}" 
                        class="form-control shadow-sm" required autofocus>
                  </div>

                  <div class="form-group mb-4">
                     <label for="password" class="form-label small">Password</label>
                     <input type="password" name="password" id="password"
                        class="form-control shadow-sm" required autocomplete="current-password">
                  </div>

                  <div class="d-grid">
                     <button type="submit" class="btn btn-dark">Sign In</button>
                  </div>
               </form>
            </div>
         </div>

         <!-- Right Side (Image Section) -->
         <div class="col-md-6 d-none d-md-block p-0">
            <img src="{{ asset('images/login.png') }}" 
                 alt="City Background" 
                 class="img-fluid w-100 vh-100 object-fit-cover">
         </div>
      </div>
   </section>
</x-guest-layout>
