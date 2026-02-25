<div class="main-sidebar">
    <aside id="sidebar-wrapper">
        <div class="sidebar-brand">
            <a href="/" title="{{ trans('admin/main.show_website') }}">
                @if(!empty($generalSettings['site_name']))
                    {{ strtoupper($generalSettings['site_name']) }}
                @else
                    Platform Title
                @endif
            </a>
        </div>
        <div class="sidebar-brand sidebar-brand-sm">
            <a href="/" title="{{ trans('admin/main.show_website') }}">
                @if(!empty($generalSettings['site_name']))
                    {{ strtoupper(substr($generalSettings['site_name'],0,2)) }}
                @endif
            </a>
        </div>

        <ul class="sidebar-menu">
            @can('admin_general_dashboard_show')
                <li class="{{ (request()->is(getAdminPanelUrl('/'))) ? 'active' : '' }}">
                    <a href="{{ getAdminPanelUrl('') }}" class="nav-link" data-toggle="tooltip" data-placement="right" title="{{ trans('admin/main.dashboard') }}">
                        <i class="fas fa-fire"></i>
                        <span>{{ trans('admin/main.dashboard') }}</span>
                    </a>
                </li>
            @endcan

            @if($authUser->can('admin_webinars') or
                $authUser->can('admin_quizzes') or
                $authUser->can('admin_reviews_lists') or
                $authUser->can('admin_webinar_assignments') or
                $authUser->can('admin_enrollment')
            )
                <li class="menu-header">{{ trans('site.education') }}</li>
            @endif

            @can('admin_webinars')
                <li class="nav-item dropdown {{ (request()->is(getAdminPanelUrl('/webinars*', false)) and !request()->is(getAdminPanelUrl('/webinars/comments*', false))) ? 'active' : '' }}">
                    <a href="#" class="nav-link has-dropdown" data-toggle="dropdown">
                        <i class="fas fa-graduation-cap"></i>
                        <span>{{ trans('panel.classes') }}</span>
                    </a>
                    <ul class="dropdown-menu">
                        @can('admin_webinars_list')
                            <li class="{{ (request()->is(getAdminPanelUrl('/webinars', false)) and request()->get('type') == 'course') ? 'active' : '' }}">
                                <a class="nav-link @if(!empty($sidebarBeeps['courses']) and $sidebarBeeps['courses']) beep beep-sidebar @endif" href="{{ getAdminPanelUrl() }}/webinars?type=course">{{ trans('admin/main.courses') }}</a>
                            </li>

                            <li class="{{ (request()->is(getAdminPanelUrl('/webinars', false)) and request()->get('type') == 'webinar') ? 'active' : '' }}">
                                <a class="nav-link @if(!empty($sidebarBeeps['webinars']) and $sidebarBeeps['webinars']) beep beep-sidebar @endif" href="{{ getAdminPanelUrl() }}/webinars?type=webinar">{{ trans('admin/main.live_classes') }}</a>
                            </li>

                            <li class="{{ (request()->is(getAdminPanelUrl('/webinars', false)) and request()->get('type') == 'text_lesson') ? 'active' : '' }}">
                                <a class="nav-link @if(!empty($sidebarBeeps['textLessons']) and $sidebarBeeps['textLessons']) beep beep-sidebar @endif" href="{{ getAdminPanelUrl() }}/webinars?type=text_lesson">{{ trans('admin/main.text_courses') }}</a>
                            </li>
                        @endcan()

                        @can('admin_webinars_create')
                            <li class="{{ (request()->is(getAdminPanelUrl('/webinars/create', false))) ? 'active' : '' }}">
                                <a class="nav-link" href="{{ getAdminPanelUrl() }}/webinars/create">{{ trans('admin/main.new') }}</a>
                            </li>
                        @endcan()

                        @can('admin_agora_history_list')
                            <li class="{{ (request()->is(getAdminPanelUrl('/agora_history', false))) ? 'active' : '' }}">
                                <a class="nav-link" href="{{ getAdminPanelUrl() }}/agora_history">{{ trans('update.agora_history') }}</a>
                            </li>
                        @endcan

                    </ul>
                </li>
            @endcan()

            @can('admin_quizzes')
                <li class="{{ (request()->is(getAdminPanelUrl('/quizzes*', false))) ? 'active' : '' }}">
                    <a class="nav-link " href="{{ getAdminPanelUrl() }}/quizzes">
                        <i class="fas fa-file"></i>
                        <span>{{ trans('admin/main.quizzes') }}</span>
                    </a>
                </li>
            @endcan()

            @can('admin_webinar_assignments')
                <li class="{{ (request()->is(getAdminPanelUrl('/assignments', false))) ? 'active' : '' }}">
                    <a href="{{ getAdminPanelUrl() }}/assignments" class="nav-link">
                        <i class="fas fa-pen"></i>
                        <span>{{ trans('update.assignments') }}</span>
                    </a>
                </li>
            @endcan

            @can('admin_enrollment')
                <li class="nav-item dropdown {{ (request()->is(getAdminPanelUrl('/enrollments*', false))) ? 'active' : '' }}">
                    <a href="#" class="nav-link has-dropdown" data-toggle="dropdown">
                        <i class="fas fa-user-plus"></i>
                        <span>{{ trans('update.enrollment') }}</span>
                    </a>
                    <ul class="dropdown-menu">
                        @can('admin_enrollment_history')
                            <li class="{{ (request()->is(getAdminPanelUrl('/enrollments/history', false))) ? 'active' : '' }}">
                                <a class="nav-link" href="{{ getAdminPanelUrl() }}/enrollments/history">{{ trans('public.history') }}</a>
                            </li>
                        @endcan

                        @can('admin_enrollment_add_student_to_items')
                            <li class="{{ (request()->is(getAdminPanelUrl('/enrollments/add-student-to-class', false))) ? 'active' : '' }}">
                                <a class="nav-link" href="{{ getAdminPanelUrl() }}/enrollments/add-student-to-class">{{ trans('update.add_student_to_a_class') }}</a>
                            </li>
                        @endcan
                    </ul>
                </li>
            @endcan

            @can('admin_reviews_lists')
                <li class="{{ (request()->is(getAdminPanelUrl('/reviews', false))) ? 'active' : '' }}">
                    <a href="{{ getAdminPanelUrl() }}/reviews" class="nav-link @if(!empty($sidebarBeeps['reviews']) and $sidebarBeeps['reviews']) beep beep-sidebar @endif">
                        <i class="fas fa-star"></i>
                        <span>{{ trans('admin/main.reviews') }}</span>
                    </a>
                </li>
            @endcan






            @if($authUser->can('admin_users') or
                $authUser->can('admin_roles') or
                $authUser->can('admin_group') or
                $authUser->can('admin_users_badges') or
                $authUser->can('admin_become_instructors_list') or
                $authUser->can('admin_delete_account_requests')
            )
                <li class="menu-header">{{ trans('panel.users') }}</li>
            @endif

            @can('admin_users')
                <li class="nav-item dropdown {{ (request()->is(getAdminPanelUrl('/students', false)) or request()->is(getAdminPanelUrl('/instructors', false))) ? 'active' : '' }}">
                    <a href="#" class="nav-link has-dropdown" data-toggle="dropdown">
                        <i class="fas fa-users"></i>
                        <span>{{ trans('admin/main.users_list') }}</span>
                    </a>

                    <ul class="dropdown-menu">
                        @can('admin_users_list')
                            <li class="{{ (request()->is(getAdminPanelUrl('/students', false))) ? 'active' : '' }}">
                                <a class="nav-link" href="{{ getAdminPanelUrl() }}/students">{{ trans('public.students') }}</a>
                            </li>
                        @endcan()

                        @can('admin_instructors_list')
                            <li class="{{ (request()->is(getAdminPanelUrl('/instructors', false))) ? 'active' : '' }}">
                                <a class="nav-link" href="{{ getAdminPanelUrl() }}/instructors">{{ trans('home.instructors') }}</a>
                            </li>
                        @endcan()

                        @can('admin_users_create')
                            <li class="{{ (request()->is(getAdminPanelUrl('/users/create', false))) ? 'active' : '' }}">
                                <a class="nav-link" href="{{ getAdminPanelUrl() }}/users/create">{{ trans('admin/main.new') }}</a>
                            </li>
                        @endcan()
                    </ul>
                </li>
            @endcan


            @can('admin_delete_account_requests')
                <li class="nav-item {{ (request()->is(getAdminPanelUrl('/users/delete-account-requests*', false))) ? 'active' : '' }}">
                    <a href="{{ getAdminPanelUrl() }}/users/delete-account-requests" class="nav-link">
                        <i class="fa fa-user-times"></i>
                        <span>{{ trans('update.delete-account-requests') }}</span>
                    </a>
                </li>
            @endcan

            @can('admin_roles')
                <li class="nav-item dropdown {{ (request()->is(getAdminPanelUrl('/roles*', false))) ? 'active' : '' }}">
                    <a href="#" class="nav-link has-dropdown" data-toggle="dropdown">
                        <i class="fas fa-user-circle"></i> <span>{{ trans('admin/main.roles') }}</span>
                    </a>
                    <ul class="dropdown-menu">
                        @can('admin_roles_list')
                            <li class="{{ (request()->is(getAdminPanelUrl('/roles', false))) ? 'active' : '' }}">
                                <a class="nav-link active" href="{{ getAdminPanelUrl() }}/roles">{{ trans('admin/main.lists') }}</a>
                            </li>
                        @endcan()
                        @can('admin_roles_create')
                            <li class="{{ (request()->is(getAdminPanelUrl('/roles/create', false))) ? 'active' : '' }}">
                                <a class="nav-link" href="{{ getAdminPanelUrl() }}/roles/create">{{ trans('admin/main.new') }}</a>
                            </li>
                        @endcan()
                    </ul>
                </li>
            @endcan()

            @can('admin_group')
                <li class="nav-item dropdown {{ (request()->is(getAdminPanelUrl('/users/groups*', false))) ? 'active' : '' }}">
                    <a href="#" class="nav-link has-dropdown" data-toggle="dropdown">
                        <i class="fas fa-sitemap"></i>
                        <span>{{ trans('admin/main.groups') }}</span>
                    </a>
                    <ul class="dropdown-menu">
                        @can('admin_group_list')
                            <li class="{{ (request()->is(getAdminPanelUrl('/users/groups', false))) ? 'active' : '' }}">
                                <a class="nav-link" href="{{ getAdminPanelUrl() }}/users/groups">{{ trans('admin/main.lists') }}</a>
                            </li>
                        @endcan
                        @can('admin_group_create')
                            <li class="{{ (request()->is(getAdminPanelUrl('/users/groups/create', false))) ? 'active' : '' }}">
                                <a class="nav-link" href="{{ getAdminPanelUrl() }}/users/groups/create">{{ trans('admin/main.new') }}</a>
                            </li>
                        @endcan
                    </ul>
                </li>
            @endcan

            @can('admin_users_badges')
                <li class="{{ (request()->is(getAdminPanelUrl('/users/badges', false))) ? 'active' : '' }}">
                    <a class="nav-link" href="{{ getAdminPanelUrl() }}/users/badges">
                        <i class="fas fa-trophy"></i>
                        <span>{{ trans('admin/main.badges') }}</span>
                    </a>
                </li>
            @endcan()



            @can('admin_become_instructors_list')
                <li class="nav-item dropdown {{ (request()->is(getAdminPanelUrl('/users/become-instructors*', false))) ? 'active' : '' }}">
                    <a href="#" class="nav-link has-dropdown" data-toggle="dropdown">
                        <i class="fas fa-list-alt"></i>
                        <span>{{ trans('admin/main.instructor_requests') }}</span>
                    </a>
                    <ul class="dropdown-menu">
                        <li class="{{ (request()->is(getAdminPanelUrl('/users/become-instructors/instructors', false))) ? 'active' : '' }}">
                            <a class="nav-link" href="{{ getAdminPanelUrl() }}/users/become-instructors/instructors">
                                <span>{{ trans('admin/main.instructors') }}</span>
                            </a>
                        </li>

                        <li class="{{ (request()->is(getAdminPanelUrl('/users/become-instructors/organizations', false))) ? 'active' : '' }}">
                            <a class="nav-link" href="{{ getAdminPanelUrl() }}/users/become-instructors/organizations">
                                <span>{{ trans('admin/main.organizations') }}</span>
                            </a>
                        </li>
                    </ul>
                </li>
            @endcan()

            @if($authUser->can('admin_supports') or
                $authUser->can('admin_comments') or
                $authUser->can('admin_reports') or
                $authUser->can('admin_contacts') or
                $authUser->can('admin_notifications')
            )
                <li class="menu-header">{{ trans('admin/main.crm') }}</li>
            @endif

            @can('admin_supports')
                <li class="nav-item dropdown {{ (request()->is(getAdminPanelUrl('/supports*', false)) and request()->get('type') != 'course_conversations') ? 'active' : '' }}">
                    <a href="#" class="nav-link has-dropdown" data-toggle="dropdown">
                        <i class="fas fa-headphones"></i>
                        <span>{{ trans('admin/main.supports') }}</span>
                    </a>

                    <ul class="dropdown-menu">
                        @can('admin_supports_list')
                            <li class="{{ (request()->is(getAdminPanelUrl('/supports', false))) ? 'active' : '' }}">
                                <a class="nav-link" href="{{ getAdminPanelUrl() }}/supports">{{ trans('public.tickets') }}</a>
                            </li>
                        @endcan

                        @can('admin_support_send')
                            <li class="{{ (request()->is(getAdminPanelUrl('/supports/create', false))) ? 'active' : '' }}">
                                <a class="nav-link" href="{{ getAdminPanelUrl() }}/supports/create">{{ trans('admin/main.new_ticket') }}</a>
                            </li>
                        @endcan

                        @can('admin_support_departments')
                            <li class="{{ (request()->is(getAdminPanelUrl('/supports/departments', false))) ? 'active' : '' }}">
                                <a class="nav-link" href="{{ getAdminPanelUrl() }}/supports/departments">{{ trans('admin/main.departments') }}</a>
                            </li>
                        @endcan
                    </ul>
                </li>

                @can('admin_support_course_conversations')
                    <li class="{{ (request()->is(getAdminPanelUrl('/supports*', false)) and request()->get('type') == 'course_conversations') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ getAdminPanelUrl() }}/supports?type=course_conversations">
                            <i class="fas fa-envelope-square"></i>
                            <span>{{ trans('admin/main.classes_conversations') }}</span>
                        </a>
                    </li>
                @endcan
            @endcan

            @can('admin_comments')
                <li class="nav-item dropdown {{ (!request()->is(getAdminPanelUrl('admin/comments/products, false')) and (request()->is(getAdminPanelUrl('/comments*', false)) and !request()->is(getAdminPanelUrl('/comments/webinars/reports', false)))) ? 'active' : '' }}">
                    <a href="#" class="nav-link has-dropdown"><i class="fas fa-comments"></i> <span>{{ trans('admin/main.comments') }}</span></a>
                    <ul class="dropdown-menu">
                        @can('admin_webinar_comments')
                            <li class="{{ (request()->is(getAdminPanelUrl('/comments/webinars', false))) ? 'active' : '' }}">
                                <a class="nav-link @if(!empty($sidebarBeeps['classesComments']) and $sidebarBeeps['classesComments']) beep beep-sidebar @endif" href="{{ getAdminPanelUrl() }}/comments/webinars">{{ trans('admin/main.classes_comments') }}</a>
                            </li>
                        @endcan

                        @can('admin_bundle_comments')
                            <li class="{{ (request()->is(getAdminPanelUrl('/comments/bundles', false))) ? 'active' : '' }}">
                                <a class="nav-link @if(!empty($sidebarBeeps['bundleComments']) and $sidebarBeeps['bundleComments']) beep beep-sidebar @endif" href="{{ getAdminPanelUrl() }}/comments/bundles">{{ trans('update.bundle_comments') }}</a>
                            </li>
                        @endcan

                        @can('admin_blog_comments')
                            <li class="{{ (request()->is(getAdminPanelUrl('/comments/blog', false))) ? 'active' : '' }}">
                                <a class="nav-link @if(!empty($sidebarBeeps['blogComments']) and $sidebarBeeps['blogComments']) beep beep-sidebar @endif" href="{{ getAdminPanelUrl() }}/comments/blog">{{ trans('admin/main.blog_comments') }}</a>
                            </li>
                        @endcan

                    </ul>
                </li>
            @endcan

            @can('admin_reports')
                <li class="nav-item dropdown {{ (request()->is(getAdminPanelUrl('/reports*', false)) or request()->is(getAdminPanelUrl('/comments/webinars/reports', false)) or request()->is(getAdminPanelUrl('/comments/blog/reports', false))) ? 'active' : '' }}">
                    <a href="#" class="nav-link has-dropdown"><i class="fas fa-info-circle"></i> <span>{{ trans('admin/main.reports') }}</span></a>

                    <ul class="dropdown-menu">
                        @can('admin_webinar_reports')
                            <li class="{{ (request()->is(getAdminPanelUrl('/reports/webinars', false))) ? 'active' : '' }}">
                                <a class="nav-link" href="{{ getAdminPanelUrl() }}/reports/webinars">{{ trans('panel.classes') }}</a>
                            </li>
                        @endcan

                        @can('admin_webinar_comments_reports')
                            <li class="{{ (request()->is(getAdminPanelUrl('/comments/webinars/reports', false))) ? 'active' : '' }}">
                                <a class="nav-link" href="{{ getAdminPanelUrl() }}/comments/webinars/reports">{{ trans('admin/main.classes_comments_reports') }}</a>
                            </li>
                        @endcan

                        @can('admin_blog_comments_reports')
                            <li class="{{ (request()->is(getAdminPanelUrl('/comments/blog/reports', false))) ? 'active' : '' }}">
                                <a class="nav-link" href="{{ getAdminPanelUrl() }}/comments/blog/reports">{{ trans('admin/main.blog_comments_reports') }}</a>
                            </li>
                        @endcan

                        @can('admin_report_reasons')
                            <li class="{{ (request()->is(getAdminPanelUrl('/reports/reasons', false))) ? 'active' : '' }}">
                                <a class="nav-link" href="{{ getAdminPanelUrl() }}/reports/reasons">{{ trans('admin/main.report_reasons') }}</a>
                            </li>
                        @endcan()

                    </ul>
                </li>
            @endcan

            @can('admin_contacts')
                <li class="{{ (request()->is(getAdminPanelUrl('/contacts*', false))) ? 'active' : '' }}">
                    <a class="nav-link" href="{{ getAdminPanelUrl() }}/contacts">
                        <i class="fas fa-phone-square"></i>
                        <span>{{ trans('admin/main.contacts') }}</span>
                    </a>
                </li>
            @endcan

            @can('admin_contacts')
                <li class="nav-item dropdown {{ (request()->is(getAdminPanelUrl('/notifications*', false))) ? 'active' : '' }}">
                    <a href="#" class="nav-link has-dropdown" data-toggle="dropdown">
                        <i class="fas fa-bell"></i>
                        <span>{{ trans('admin/main.notifications') }}</span>
                    </a>

                    <ul class="dropdown-menu">
                        @can('admin_notifications_list')
                            <li class="{{ (request()->is(getAdminPanelUrl('/notifications', false))) ? 'active' : '' }}">
                                <a class="nav-link" href="{{ getAdminPanelUrl() }}/notifications">{{ trans('public.history') }}</a>
                            </li>
                        @endcan

                        @can('admin_notifications_posted_list')
                            <li class="{{ (request()->is(getAdminPanelUrl('/notifications/posted', false))) ? 'active' : '' }}">
                                <a class="nav-link" href="{{ getAdminPanelUrl() }}/notifications/posted">{{ trans('admin/main.posted') }}</a>
                            </li>
                        @endcan

                        @can('admin_notifications_send')
                            <li class="{{ (request()->is(getAdminPanelUrl('/notifications/send', false))) ? 'active' : '' }}">
                                <a class="nav-link" href="{{ getAdminPanelUrl() }}/notifications/send">{{ trans('admin/main.new') }}</a>
                            </li>
                        @endcan

                        @can('admin_notifications_templates')
                            <li class="{{ (request()->is(getAdminPanelUrl('/notifications/templates', false))) ? 'active' : '' }}">
                                <a class="nav-link" href="{{ getAdminPanelUrl() }}/notifications/templates">{{ trans('admin/main.templates') }}</a>
                            </li>
                        @endcan

                        @can('admin_notifications_template_create')
                            <li class="{{ (request()->is(getAdminPanelUrl('/notifications/templates/create', false))) ? 'active' : '' }}">
                                <a class="nav-link" href="{{ getAdminPanelUrl() }}/notifications/templates/create">{{ trans('admin/main.new_template') }}</a>
                            </li>
                        @endcan
                    </ul>
                </li>
            @endcan

            @if($authUser->can('admin_documents') or
                $authUser->can('admin_sales_list') or
                $authUser->can('admin_payouts') or
                $authUser->can('admin_offline_payments_list') or
                $authUser->can('admin_subscribe')
            )
                <li class="menu-header">{{ trans('admin/main.financial') }}</li>
            @endif

            @can('admin_documents')
                <li class="nav-item dropdown {{ (request()->is(getAdminPanelUrl('/financial/documents*', false))) ? 'active' : '' }}">
                    <a href="#" class="nav-link has-dropdown" data-toggle="dropdown">
                        <i class="fas fa-file-invoice-dollar"></i>
                        <span>{{ trans('admin/main.balances') }}</span>
                    </a>
                    <ul class="dropdown-menu">

                        @can('admin_documents_list')
                            <li class="{{ (request()->is(getAdminPanelUrl('/financial/documents', false))) ? 'active' : '' }}">
                                <a class="nav-link" href="{{ getAdminPanelUrl() }}/financial/documents">{{ trans('admin/main.list') }}</a>
                            </li>
                        @endcan

                        @can('admin_documents_create')
                            <li class="{{ (request()->is(getAdminPanelUrl('/financial/documents/new', false))) ? 'active' : '' }}">
                                <a class="nav-link" href="{{ getAdminPanelUrl() }}/financial/documents/new">{{ trans('admin/main.new') }}</a>
                            </li>
                        @endcan
                    </ul>
                </li>
            @endcan

            @can('admin_sales_list')
                <li class="{{ (request()->is(getAdminPanelUrl('/financial/sales*', false))) ? 'active' : '' }}">
                    <a href="{{ getAdminPanelUrl() }}/financial/sales" class="nav-link">
                        <i class="fas fa-list-ul"></i>
                        <span>{{ trans('admin/main.sales_list') }}</span>
                    </a>
                </li>
            @endcan

            @can('admin_payouts')
                <li class="nav-item dropdown {{ (request()->is(getAdminPanelUrl('/financial/payouts*', false))) ? 'active' : '' }}">
                    <a href="#" class="nav-link has-dropdown"><i class="fas fa-credit-card"></i> <span>{{ trans('admin/main.payout') }}</span></a>
                    <ul class="dropdown-menu">
                        @can('admin_payouts_list')
                            <li class="{{ (request()->is(getAdminPanelUrl('/financial/payouts', false)) and request()->get('payout') == 'requests') ? 'active' : '' }}">
                                <a href="{{ getAdminPanelUrl() }}/financial/payouts?payout=requests" class="nav-link @if(!empty($sidebarBeeps['payoutRequest']) and $sidebarBeeps['payoutRequest']) beep beep-sidebar @endif">
                                    <span>{{ trans('panel.requests') }}</span>
                                </a>
                            </li>
                        @endcan

                        @can('admin_payouts_list')
                            <li class="{{ (request()->is(getAdminPanelUrl('/financial/payouts', false)) and request()->get('payout') == 'history') ? 'active' : '' }}">
                                <a href="{{ getAdminPanelUrl() }}/financial/payouts?payout=history" class="nav-link">
                                    <span>{{ trans('public.history') }}</span>
                                </a>
                            </li>
                        @endcan
                    </ul>
                </li>
            @endcan

            @can('admin_offline_payments_list')
                <li class="nav-item dropdown {{ (request()->is(getAdminPanelUrl('/financial/offline_payments*', false))) ? 'active' : '' }}">
                    <a href="#" class="nav-link has-dropdown"><i class="fas fa-university"></i> <span>{{ trans('admin/main.offline_payments') }}</span></a>
                    <ul class="dropdown-menu">
                        <li class="{{ (request()->is(getAdminPanelUrl('/financial/offline_payments', false)) and request()->get('page_type') == 'requests') ? 'active' : '' }}">
                            <a href="{{ getAdminPanelUrl() }}/financial/offline_payments?page_type=requests" class="nav-link @if(!empty($sidebarBeeps['offlinePayments']) and $sidebarBeeps['offlinePayments']) beep beep-sidebar @endif">
                                <span>{{ trans('panel.requests') }}</span>
                            </a>
                        </li>

                        <li class="{{ (request()->is(getAdminPanelUrl('/financial/offline_payments', false)) and request()->get('page_type') == 'history') ? 'active' : '' }}">
                            <a href="{{ getAdminPanelUrl() }}/financial/offline_payments?page_type=history" class="nav-link">
                                <span>{{ trans('public.history') }}</span>
                            </a>
                        </li>
                    </ul>
                </li>
            @endcan

            @can('admin_subscribe')
                <li class="nav-item dropdown {{ (request()->is(getAdminPanelUrl('/financial/subscribes*', false))) ? 'active' : '' }}">
                    <a href="#" class="nav-link has-dropdown" data-toggle="dropdown">
                        <i class="fas fa-cart-plus"></i>
                        <span>{{ trans('admin/main.subscribes') }}</span>
                    </a>
                    <ul class="dropdown-menu">
                        @can('admin_subscribe_list')
                            <li class="{{ (request()->is(getAdminPanelUrl('/financial/subscribes', false))) ? 'active' : '' }}">
                                <a class="nav-link" href="{{ getAdminPanelUrl() }}/financial/subscribes">{{ trans('admin/main.packages') }}</a>
                            </li>
                        @endcan

                        @can('admin_subscribe_create')
                            <li class="{{ (request()->is(getAdminPanelUrl('/financial/subscribes/new', false))) ? 'active' : '' }}">
                                <a class="nav-link" href="{{ getAdminPanelUrl() }}/financial/subscribes/new">{{ trans('admin/main.new_package') }}</a>
                            </li>
                        @endcan
                    </ul>
                </li>
            @endcan


            @if($authUser->can('admin_settings'))
                <li class="menu-header">{{ trans('admin/main.settings') }}</li>
            @endif

            @can('admin_general_dashboard_show')
                <li class="{{ (request()->is(getAdminPanelUrl('/health-log*', false)) and !request()->is(getAdminPanelUrl('/course-health-log-settings*', false)) and !request()->is(getAdminPanelUrl('/student-health-logs*', false))) ? 'active' : '' }}">
                    <a href="{{ getAdminPanelUrl() }}/health-log" class="nav-link">
                        <i class="fas fa-heartbeat"></i>
                        <span>{{ trans('admin/main.health_log') ?? 'Health Log' }}</span>
                    </a>
                </li>
                <li class="{{ (request()->is(getAdminPanelUrl('/course-health-log-settings*', false))) ? 'active' : '' }}">
                    <a href="{{ getAdminPanelUrl() }}/course-health-log-settings" class="nav-link">
                        <i class="fas fa-book-medical"></i>
                        <span>{{ trans('admin/main.course_health_log_settings') ?? 'Course health log settings' }}</span>
                    </a>
                </li>
                <li class="{{ (request()->is(getAdminPanelUrl('/student-health-logs*', false))) ? 'active' : '' }}">
                    <a href="{{ getAdminPanelUrl() }}/student-health-logs" class="nav-link">
                        <i class="fas fa-clipboard-list"></i>
                        <span>{{ trans('admin/main.student_health_logs') ?? 'Student health logs' }}</span>
                    </a>
                </li>
            @endcan

            @can('admin_settings')
                @php
                    $settingClass ='';

                    if (request()->is(getAdminPanelUrl('/settings*', false)) and
                            !(
                                request()->is(getAdminPanelUrl('/settings/404', false)) or
                                request()->is(getAdminPanelUrl('/settings/contact_us', false)) or
                                request()->is(getAdminPanelUrl('/settings/footer', false)) or
                                request()->is(getAdminPanelUrl('/settings/navbar_links', false))
                            )
                        ) {
                            $settingClass = 'active';
                        }
                @endphp

                <li class="nav-item {{ $settingClass ?? '' }}">
                    <a href="{{ getAdminPanelUrl() }}/settings" class="nav-link">
                        <i class="fas fa-cogs"></i>
                        <span>{{ trans('admin/main.settings') }}</span>
                    </a>
                </li>
            @endcan()


            <li>
                <a class="nav-link" href="{{ getAdminPanelUrl() }}/logout">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>{{ trans('admin/main.logout') }}</span>
                </a>
            </li>

        </ul>
        <br><br><br>
    </aside>
</div>
