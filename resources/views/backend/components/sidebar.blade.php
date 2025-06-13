 <aside id="sidebar" class="sidebar">

     <ul class="sidebar-nav" id="sidebar-nav">

         @if(Auth::guard('admin')->check())

         <li class="nav-item ">
             <a class="nav-link {{ Request::is('admin/dashboard') ? '' : 'collapsed' }}" href="{{ route('admin.dashboard') }}">
                 <i class="bi bi-grid"></i>
                 <span>Dashboard</span>
             </a>
         </li><!-- End Dashboard Nav -->

         <li class="nav-item ">
             <a class="nav-link  {{ Request::is('admin/sliders*') ? '' : 'collapsed' }}" href="{{ route('admin.sliders.index') }}">
                 <i class="bi bi-images"></i>
                 <span>Sliders</span>
             </a>
         </li>

         <li class="nav-item">
             <a class="nav-link  {{ Request::is('admin/features*') ? '' : 'collapsed' }}" href="{{ route('admin.features.index') }}">
                 <i class="bi bi-star"></i>
                 <span>Features</span>
             </a>
         </li>

         <li class="nav-item ">
             <a class="nav-link {{ Request::is('admin/amenities*') ? '' : 'collapsed' }}" href="{{ route('admin.amenities.index') }}">
                 <i class="bi bi-stars"></i>
                 <span>Amenities</span>
             </a>
         </li>

         <li class="nav-item">
             <a class="nav-link  {{ Request::is('admin/properties*') ? '' : 'collapsed' }}" href="{{ route('admin.properties.index') }}">
                 <i class="bi bi-building"></i>
                 <span>Properties</span>
             </a>
         </li>

         <li class="nav-heading">User</li>

         <li class="nav-item ">
             <a class="nav-link {{ Request::is('admin/owner*') ? '' : 'collapsed' }}" href="{{ route('admin.owner.index') }}">
                 <i class="bi bi-people"></i>
                 <span>Owner</span>
             </a>
         </li>

         <li class="nav-item ">
             <a class="nav-link {{ Request::is('admin/admin-list*') ? '' : 'collapsed' }}" href="{{ route('admin.admin-list.index') }}">
                 <i class="bi bi-person-circle"></i>
                 <span>Admins</span>
             </a>
         </li>

         <li class="nav-item ">
             <a class="nav-link {{ Request::is('admin/user*') ? '' : 'collapsed' }}" href="{{ route('admin.user.index') }}">
                 <i class="bi bi-people-fill"></i>
                 <span>Users</span>
             </a>
         </li>

         <li class="nav-item">
             <a class="nav-link  {{ Request::is('admin/staff*') ? '' : 'collapsed' }}" href="{{ route('admin.staff.index') }}">
                 <i class="bi bi-briefcase"></i>
                 <span>Staff</span>
             </a>
         </li>

         <li class="nav-item ">
             <a class="nav-link {{ Request::is('admin/field_manager*') ? '' : 'collapsed' }}" href="{{ route('admin.field_manager.index') }}">
                 <i class="bi bi-person-badge"></i>
                 <span>Field Manager</span>
             </a>
         </li>

         <!--<li class="nav-heading">Schedule</li>-->

         <!--<li class="nav-item ">-->
         <!--    <a class="nav-link {{ Request::is('admin/schedule_visit*') ? '' : 'collapsed' }}" href="{{ route('admin.schedule_visit.index') }}">-->
         <!--        <i class="bi bi-calendar-event"></i>-->
         <!--        <span>Schedule Visit</span>-->
         <!--    </a>-->
         <!--</li>-->

         <!--<li class="nav-item ">-->
         <!--    <a class="nav-link {{ Request::is('admin/schedule_properties*') ? '' : 'collapsed' }}" href="{{ route('admin.schedule_properties.index') }}">-->
         <!--        <i class="bi bi-person-check"></i>-->
         <!--        <span>Visiter</span>-->
         <!--    </a>-->
         <!--</li>-->
         <li class="nav-heading">Payments</li>

         <li class="nav-item ">
            <a class="nav-link {{ Request::is('admin/invoices*') ? '' : 'collapsed' }}" href="{{ route('admin.invoice.list') }}">
                <i class="bi bi-person-check"></i>
                <span>Invoice</span>
            </a>
         </li>

         <li class="nav-heading">Blog</li>

         <li class="nav-item ">
             <a class="nav-link {{ Request::is('admin/categories*') ? '' : 'collapsed' }}" href="{{ route('admin.categories.index') }}">
                 <i class="bi bi-tags"></i>
                 <span>Categories</span>
             </a>
         </li>

         <li class="nav-item">
             <a class="nav-link {{ Request::is('admin/tags*') ? '' : 'collapsed' }}" href="{{ route('admin.tags.index') }}">
                 <i class="bi bi-tag"></i>
                 <span>Tags</span>
             </a>
         </li>

         <li class="nav-item">
             <a class="nav-link {{ Request::is('admin/posts*') ? '' : 'collapsed' }}" href="{{ route('admin.posts.index') }}">
                 <i class="bi bi-journal-text"></i>
                 <span>Posts</span>
             </a>
         </li>

         <li class="nav-item">
             <a class="nav-link {{ Request::is('admin/society*') ? '' : 'collapsed' }}" href="{{ route('admin.society.index') }}">
                 <i class="bi bi-building"></i>
                 <span>Society</span>
             </a>
         </li>

         <li class="nav-item ">
             <a class="nav-link {{ Request::is('admin/locality*') ? '' : 'collapsed' }}" href="{{ route('admin.locality.index') }}">
                 <i class="bi bi-map"></i>
                 <span>Locality</span>
             </a>
         </li>

         <li class="nav-heading">Help</li>

         <li class="nav-item ">
             <a class="nav-link {{ Request::is('admin/contact*') ? '' : 'collapsed' }}" href="{{ route('admin.contact.index') }}">
                 <i class="bi bi-envelope"></i>
                 <span>Contact</span>
             </a>
         </li>

         <li class="nav-item ">
             <a class="nav-link {{ Request::is('admin/inquery*') ? '' : 'collapsed' }}" href="{{ route('admin.inquery.index') }}">
                 <i class="bi bi-question-circle"></i>
                 <span>Inquiry</span>
             </a>
         </li>

         <li class="nav-heading">Settings</li>
         <li class="nav-item">
               <a class="nav-link {{ Request::is('admin/changepassword*') ? '' : 'collapsed' }} {{ Request::is('admin/profile*') ? '' : 'collapsed' }}" data-bs-target="#charts-nav" data-bs-toggle="collapse" href="#" aria-expanded="true">
                 <i class="bi bi-gear"></i><span>Settings</span><i class="bi bi-chevron-down ms-auto"></i>
             </a>
             <ul id="charts-nav" class="nav-content collapse show" data-bs-parent="#sidebar-nav">
                 <li>
                     <a href="{{ route('admin.changepassword') }}" class="{{ Request::is('admin/changepassword') ? 'active' : '' }}">
                         <i class="bi bi-circle"></i><span>Change Password</span>
                     </a>
                 </li>
                 <li>
                     <a href="{{ route('admin.profile') }}" class="{{ Request::is('admin/profile') ? 'active' : '' }}">
                         <i class="bi bi-circle"></i><span>Profile</span>
                     </a>
                 </li>
             </ul>
         </li>


         @endif
         @if(Auth::guard('staff')->check())
         @php
            $permission = \App\Models\StaffPermission::where('id', \Illuminate\Support\Facades\Auth::guard('staff')->id())->first();
        @endphp
        @if ($permission)

         <li class="nav-item ">
             <a class="nav-link {{ Request::is('staff/dashboard') ? '' : 'collapsed' }}" href="{{ route('staff.dashboard') }}">
                 <i class="bi bi-speedometer2"></i>
                 <span>Dashboard</span>
             </a>
         </li>
         @if ($permission->owner_number == 1)
         <li class="nav-item ">
             <a class="nav-link {{ Request::is('staff/addNumber') ? '' : 'collapsed' }}" href="{{ route('staff.owner.add.number') }}">
                 <i class="bi bi-people"></i>
                 <span>Add Owner's Number</span>
             </a>
         </li>
         @endif
        @if ($permission->owner == 1)
         <li class="nav-item ">
             <a class="nav-link {{ Request::is('staff/owner*') ? '' : 'collapsed' }}" href="{{ route('staff.owner.index') }}">
                 <i class="bi bi-people"></i>
                 <span>Owner</span>
             </a>
         </li>
         @endif
         @if ($permission->users == 1)
         <li class="nav-item ">
             <a class="nav-link {{ Request::is('staff/user*') ? '' : 'collapsed' }}" href="{{ route('staff.user.index') }}">
                 <i class="bi bi-person"></i>
                 <span>Users</span>
             </a>
         </li>
         @endif
         @if($permission->property == 1)
         <li class="nav-item ">
             <a class="nav-link {{ Request::is('staff/properties*') ? '' : 'collapsed' }}" href="{{ route('staff.properties.index') }}">
                 <i class="bi bi-building"></i>
                 <span>Property</span>
             </a>
         </li>
         @endif
         @if($permission->fieldManager_list == 1)
         <li class="nav-item ">
             <a class="nav-link {{ Request::is('staff/field_manager*') ? '' : 'collapsed' }}" href="{{ route('staff.field_manager.index') }}">
                 <i class="bi bi-person-badge"></i>
                 <span>Field Manager</span>
             </a>
         </li>
        @endif
        @if($permission->schedule_visit == 1)
         <li class="nav-header">Schedule</li>
         <li class="nav-item ">
             <a class="nav-link {{ Request::is('staff/schedule_visit*') ? '' : 'collapsed' }}" href="{{ route('staff.schedule_visit.index') }}">
                 <i class="bi bi-calendar-check"></i>
                 <span>Schedule Visit</span>
             </a>
         </li>
         @endif
         @if($permission->visiter_list == 1)
         <li class="nav-item ">
             <a class="nav-link {{ Request::is('staff/schedule_properties*') ? '' : 'collapsed' }}" href="{{ route('staff.schedule_properties.index') }}">
                 <i class="bi bi-person-lines-fill"></i>
                 <span>Visitor</span>
             </a>
         </li>
         @endif
         @if($permission->post_list == 1)
         <li class="nav-item ">
             <a class="nav-link {{ Request::is('staff/posts*') ? '' : 'collapsed' }}" href="{{ route('staff.posts.index') }}">
                 <i class="bi bi-file-earmark-text"></i>
                 <span>Posts</span>
             </a>
         </li>
        @endif
         <li class="nav-header">Help</li>
         <li class="nav-item ">
             <a class="nav-link {{ Request::is('staff/contact*') ? '' : 'collapsed' }}" href="{{ route('staff.contact.index') }}">
                 <i class="bi bi-envelope"></i>
                 <span>Contact</span>
             </a>
         </li>
        
         @if($permission->inquiry_list == 1)
         <li class="nav-item ">
             <a class="nav-link {{ Request::is('staff/inquery*') ? '' : 'collapsed' }}" href="{{ route('staff.inquery.index') }}">
                 <i class="bi bi-question-circle"></i>
                 <span>Inquiry</span>
             </a>
         </li>
        @endif
        
        <li class="nav-item">
             <a class="nav-link {{ Request::is('staff/additional-details*') ? '' : 'collapsed' }}" href="{{ route('staff.additional-details.index') }}">
                 <i class="bi bi-tag"></i>
                 <span>Additional Details</span>
             </a>
         </li>
        @if($permission->settings == 1)
         <li class="nav-heading">Settings</li>
         <li class="nav-item">
             <a class="nav-link {{ Request::is('staff/changepassword*') ? '' : 'collapsed' }} {{ Request::is('staff/profile*') ? '' : 'collapsed' }}" data-bs-target="#setting-staff" data-bs-toggle="collapse" href="#" aria-expanded="true">
                 <i class="bi bi-gear"></i><span>Settings</span><i class="bi bi-chevron-down ms-auto"></i>
             </a>
             <ul id="setting-staff" class="nav-content collapse show" data-bs-parent="#sidebar-nav">
                 <li>
                     <a href="{{ route('staff.changepassword') }}" class="{{ Request::is('staff/changepassword') ? 'active' : '' }}">
                         <i class="bi bi-circle"></i><span>Change Password</span>
                     </a>
                 </li>
                 <li>
                     <a href="{{ route('staff.profile') }}" class="{{ Request::is('staff/profile') ? 'active' : '' }}">
                         <i class="bi bi-circle"></i><span>Profile</span>
                     </a>
                 </li>
             </ul>
         </li>
         @endif
        @endif
         @endif
         @if(Auth::guard('field_manager')->check())
            <!-- Dashboard -->
            <li class="nav-item {{ Request::is('field_manager/dashboard') ? 'active' : '' }}">
                <a class="nav-link {{ Request::is('field_manager/dashboard*') ? '' : 'collapsed' }}" href="{{ route('field_manager.dashboard') }}">
                    <i class="bi bi-speedometer2"></i>
                    <span>Dashboard</span>
                </a>
            </li>

            <!-- Visiter -->
            <li class="nav-item {{ Request::is('field_manager/visiter*') ? 'active' : '' }}">
                <a class="nav-link {{ Request::is('field_manager/visiter*') ? '' : 'collapsed' }}" href="{{ route('field_manager.visiter.index') }}">
                    <i class="bi bi-people"></i>
                    <span>Visiter</span>
                </a>
            </li>

            <li class="nav-heading">Settings</li>
            <!-- Settings with Dropdown -->
            <li class="nav-item">
             <a class="nav-link {{ Request::is('field_manager/changepassword*') ? '' : 'collapsed' }} {{ Request::is('field_manager/profile*') ? '' : 'collapsed' }}" data-bs-target="#charts-nav" data-bs-toggle="collapse" href="#" aria-expanded="true">
                 <i class="bi bi-gear"></i><span>Settings</span><i class="bi bi-chevron-down ms-auto"></i>
             </a>
             <ul id="charts-nav" class="nav-content collapse show" data-bs-parent="#sidebar-nav">
                 <li>
                     <a href="{{ route('field_manager.changepassword') }}" class="{{ Request::is('field_manager/changepassword') ? 'active' : '' }}">
                         <i class="bi bi-circle"></i><span>Change Password</span>
                     </a>
                 </li>
                 <li>
                     <a href="{{ route('field_manager.profile') }}" class="{{ Request::is('field_manager/profile') ? 'active' : '' }}">
                         <i class="bi bi-circle"></i><span>Profile</span>
                     </a>
                 </li>
             </ul>
         </li>


        @endif

     </ul>

 </aside><!-- End Sidebar-->