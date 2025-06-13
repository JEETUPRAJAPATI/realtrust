  <!-- ======= Header ======= -->
  <header id="header" class="header fixed-top d-flex align-items-center">

      <div class="d-flex align-items-center justify-content-between">
          <a href="#" class="logo d-flex align-items-center">
              <img src="assets/img/logo.png" alt="">
              <span class="d-none d-lg-block">RealTrust</span>
          </a>
          <i class="bi bi-list toggle-sidebar-btn"></i>
      </div><!-- End Logo -->

      <div class="search-bar">
          <form class="search-form d-flex align-items-center" method="POST" action="#">
              <input type="text" name="query" placeholder="Search" title="Enter search keyword">
              <button type="submit" title="Search"><i class="bi bi-search"></i></button>
          </form>
      </div><!-- End Search Bar -->

      <nav class="header-nav ms-auto">
          <ul class="d-flex align-items-center">

              <li class="nav-item d-block d-lg-none">
                  <a class="nav-link nav-icon search-bar-toggle " href="#">
                      <i class="bi bi-search"></i>
                  </a>
              </li><!-- End Search Icon-->

              <li class="nav-item dropdown">

                  <a class="nav-link nav-icon" href="#" data-bs-toggle="dropdown">
                      <i class="bi bi-bell"></i>
                      <span class="badge bg-primary badge-number">
                          @if(Auth::guard('staff')->check())
                          {{ Auth::guard('staff')->user()->unreadNotifications->count() }}
                          @else
                          0
                          @endif
                      </span>
                  </a><!-- End Notification Icon -->

                  <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow notifications" style="max-height: 500px; overflow-y: auto;">
                        @if(Auth::guard('staff')->check())
                            @foreach(Auth::guard('staff')->user()->unreadNotifications as $notification)
                                @php
                                    $route = '';
                                    switch ($notification->type) {
                                        case 'App\Notifications\StaffNotification':
                                            $route = route('staff.properties.view', $notification->data['property_id']);
                                            break;
                                        case 'App\Notifications\PropertyVisitScheduled':
                                            $route = route('staff.schedule_properties.visit', $notification->data['property_id']);
                                            break;
                                        default:
                                            $route = route('staff.notifications.read', $notification->data['property_id']);
                                            break;
                                    }
                                @endphp
                               <li class="notification-item">
                                    <a href="{{ $route }}" class="d-flex align-items-start text-decoration-none text-dark">
                                        <i class="bi bi-info-circle text-primary me-2"></i>
                                        <div style="min-width: 20em !important;">
                                            <h4>New Message</h4>
                                            <p>{{ $notification->data['message'] }}</p>
                                            <div class="data d-flex justify-content-between">
                                                <p>{{ $notification->created_at->diffForHumans() }}</p>
                                                <form action="{{ route('notifications.markAsRead', $notification->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="badge rounded-pill bg-danger text-sm">
                                                    Mark as Read
                                                </button>
                                            </div>
                                        </form>
                                        </div>
                                    </a>
                                </li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                            @endforeach
                        @endif
                    </ul>
                    <!-- End Notification Dropdown Items -->

              </li><!-- End Notification Nav -->
              <li class="nav-item dropdown pe-3">
                  @if(Auth::guard('admin')->check())
                  <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#" data-bs-toggle="dropdown">
                  <img src="{{ Storage::disk('public')->exists('admin/' . Auth::guard('admin')->user()->image) ? Storage::url('admin/' . Auth::guard('admin')->user()->image) : asset('assets/img/defaultprofile.png') }}" 
                    class="rounded-circle" 
                    alt="{{ Auth::guard('admin')->user()->name }}" 
                    width="24" 
                    height="24">
                      <span class="d-none d-md-block dropdown-toggle ps-2">{{ strtok(Auth::guard('admin')->user()->name, " ") }}</span>
                  </a><!-- End Profile Iamge Icon -->

                  <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
                      <li class="dropdown-header">
                          <h6>{{ strtok(Auth::guard('admin')->user()->name, " ") }}</h6>
                      </li>
                      <li>
                          <hr class="dropdown-divider">
                      </li>
                      <li>
                          <a class="dropdown-item d-flex align-items-center" href="{{route('admin.profile')}}">
                              <i class="bi bi-person"></i>
                              <span>My Profile</span>
                          </a>
                      </li>
                      <li>
                          <hr class="dropdown-divider">
                      </li>
                      <li>
                          <a class="dropdown-item d-flex align-items-center" href="{{route('admin.profile')}}">
                              <i class="bi bi-gear"></i>
                              <span>Account Settings</span>
                          </a>
                      </li>
                      <li>
                          <hr class="dropdown-divider">
                      </li>
                      <li>
                          <a class="dropdown-item d-flex align-items-center" href="{{ route('admin.logout') }}"
                              onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                              <i class="bi bi-box-arrow-right"></i>
                              <span>{{ __('Sign Out') }}</span>
                          </a>
                      </li>
                      <form id="logout-form" action="{{ route('admin.logout') }}" method="POST" style="display: none;">
                          @csrf
                      </form>


                      @elseif(Auth::guard('staff')->check())

                      <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#" data-bs-toggle="dropdown">
                          <img src="{{ Storage::url('staff/' . Auth::guard('staff')->user()->image) }}" class="rounded-circle" alt="{{ Auth::guard('staff')->user()->name }}" width="24" height="24">
                          <span class="d-none d-md-block dropdown-toggle ps-2">{{ strtok(Auth::guard('staff')->user()->name, " ") }}</span>
                      </a><!-- End Profile Iamge Icon -->

                      <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
                          <li class="dropdown-header">
                              <h6>{{ strtok(Auth::guard('staff')->user()->name, " ") }}</h6>
                          </li>
                          <li>
                              <hr class="dropdown-divider">
                          </li>
                          <li>
                              <a class="dropdown-item d-flex align-items-center" href="{{route('staff.profile')}}">
                                  <i class="bi bi-person"></i>
                                  <span>My Profile</span>
                              </a>
                          </li>
                          <li>
                              <hr class="dropdown-divider">
                          </li>
                          <li>
                              <a class="dropdown-item d-flex align-items-center" href="{{route('staff.profile')}}">
                                  <i class="bi bi-gear"></i>
                                  <span>Account Settings</span>
                              </a>
                          </li>
                          <li>
                              <hr class="dropdown-divider">
                          </li>
                          <li>
                              <a class="dropdown-item d-flex align-items-center" href="{{ route('staff.logout') }}"
                                  onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                  <i class="bi bi-box-arrow-right"></i>
                                  <span>{{ __('Sign Out') }}</span>
                              </a>
                          </li>
                          <form id="logout-form" action="{{ route('staff.logout') }}" method="POST" style="display: none;">
                              @csrf
                          </form>
                          @elseif(Auth::guard('field_manager')->check())
                          <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#" data-bs-toggle="dropdown">
                              <img src="{{ Storage::url('field_manager/' . Auth::guard('field_manager')->user()->image) }}" class="rounded-circle" alt="{{ Auth::guard('field_manager')->user()->name }}" width="24" height="24">
                              <span class="d-none d-md-block dropdown-toggle ps-2">{{ strtok(Auth::guard('field_manager')->user()->name, " ") }}</span>
                          </a><!-- End Profile Iamge Icon -->

                          <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
                              <li class="dropdown-header">
                                  <h6>{{ strtok(Auth::guard('field_manager')->user()->name, " ") }}</h6>
                              </li>
                              <li>
                                  <hr class="dropdown-divider">
                              </li>
                              <li>
                                  <a class="dropdown-item d-flex align-items-center" href="{{route('field_manager.profile')}}">
                                      <i class="bi bi-person"></i>
                                      <span>My Profile</span>
                                  </a>
                              </li>
                              <li>
                                  <hr class="dropdown-divider">
                              </li>
                              <li>
                                  <a class="dropdown-item d-flex align-items-center" href="{{route('field_manager.profile')}}">
                                      <i class="bi bi-gear"></i>
                                      <span>Account Settings</span>
                                  </a>
                              </li>
                              <li>
                                  <hr class="dropdown-divider">
                              </li>
                              <li>
                                  <a class="dropdown-item d-flex align-items-center" href="{{ route('field_manager.logout') }}"
                                      onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                      <i class="bi bi-box-arrow-right"></i>
                                      <span>{{ __('Sign Out') }}</span>
                                  </a>
                              </li>
                              <form id="logout-form" action="{{ route('field_manager.logout') }}" method="POST" style="display: none;">
                                  @csrf
                              </form>
                              @endif
                          </ul><!-- End Profile Dropdown Items -->
              </li><!-- End Profile Nav -->

          </ul>
      </nav><!-- End Icons Navigation -->

  </header><!-- End Header -->