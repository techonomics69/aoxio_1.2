<div class="content-wrapper">
    
    <!-- Content Header (Page header) -->
    <?php include"include/breadcrumb.php"; ?>

    <!-- Main content -->
    <div class="content">
      <div class="container-fluid">

        <div class="row">
          <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                  <h3 class="card-title"><?php echo trans('users') ?></h3>
                    <div class="card-tools">
                      <div class="filter-bars pull-right">
                        <a class="filter-action btn btn-outline-primary text-primary"> <i class="fas fa-filter"></i></a>
                      </div>
                    </div>
                </div>

                <div class="filter_popup showFilter">
                    <p class="leads mb-3"><?php echo trans('filters') ?></p>

                    <form action="<?php echo base_url('admin/users/all_users/all') ?>" class="sort_form" method="get">
                      <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label><?php echo trans('plans') ?></label>
                                <select name="package" class="form-control form-control-sm">
                                    <option <?php if(isset($_GET['package']) && $_GET['package'] == 'all'){echo "selected";} ?> value="all"><?php echo trans('all') ?></option>
                                    <?php foreach ($packages as $package): ?>
                                        <option <?php if(isset($_GET['package']) && $_GET['package'] == $package->id){echo "selected";} ?> value="<?php echo html_escape($package->id) ?>"><?php echo html_escape($package->name) ?></option>
                                    <?php endforeach ?>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <label><?php echo trans('status') ?></label>
                                <select name="sort" class="form-control search form-control-sm">
                                    <option <?php if(isset($_GET['sort']) && $_GET['sort'] == 'all'){echo "selected";} ?> value="all"><?php echo trans('all') ?></option>
                                    <option <?php if(isset($_GET['sort']) && $_GET['sort'] == 'verified'){echo "selected";} ?> value="verified"><?php echo trans('verified') ?></option>
                                    <option <?php if(isset($_GET['sort']) && $_GET['sort'] == 'pending'){echo "selected";} ?> value="pending"><?php echo trans('pending') ?></option>
                                    <option <?php if(isset($_GET['sort']) && $_GET['sort'] == 'expired'){echo "selected";} ?> value="expired"><?php echo trans('expired') ?></option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <label><?php echo trans('name') ?></label>
                                <input type="text" name="search" class="form-control form-control-sm" placeholder="<?php echo trans('search-by-name') ?>">
                            </div>
                        </div>

                        <div class="col-md-12 mt-3">
                          <button type="submit" class="btn btn-primary btn-sm btn-block"><?php echo trans('submit') ?></button>
                        </div>

                      </div>
                    </form>
                </div>

                <div class="card-body table-responsive p-0">
                  <?php if (empty($users)): ?>
                    <?php $this->load->view('admin/include/not-found') ?>
                  <?php else: ?>
                    <table class="table table-hover m-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th><?php echo trans('user') ?></th>
                                <th><?php echo trans('status') ?></th>
                                <th><?php echo trans('plan') ?></th>
                                <th><?php echo trans('payment') ?></th>
                                <th><?php echo trans('informations') ?></th>
                                <th><?php echo trans('action') ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $i=1; foreach ($users as $user): ?>
                                <tr id="row_<?php echo html_escape($user->id) ?>">
                                    <th scope="row"><?php echo html_escape($i) ?></th>
                                    
                                    <?php if ($user->thumb == ''): ?>
                                        <?php $avatar = 'assets/images/no-photo-sm.png'; ?> 
                                    <?php else: ?>
                                        <?php $avatar = $user->thumb; ?>
                                    <?php endif ?>

                                    <td class="pl-2">
                                      <div class="d-flex align-items-center">
                                        <div>
                                          <img width="50px" class="img-circle mr-3" src="<?php echo base_url($avatar) ?>"> 
                                        </div>
                                        
                                        <div class="d-flexs flex-columns">
                                            <div>
                                                <p class="leads font-weight-bold mb-0"><?php echo ucfirst($user->name); ?></p>
                                            </div>
                                            <p class="text-muted mb-0">
                                              <?php echo html_escape($user->phone); ?> 
                                              <?php if ($user->phone_verified == 1): ?>
                                                <span class="ml-1 text-primary" data-toggle="tooltip" data-title="Phone Verified" data-placement="top"><i class="fas fa-check-circle"></i></span>
                                              <?php endif ?>
                                            </p>
                                            <p class="text-muted mb-0">
                                              <?php echo html_escape($user->email); ?>
                                              <?php if ($user->email_verified == 1): ?>
                                                <span class="ml-1 text-success" data-toggle="tooltip" data-title="Email Verified" data-placement="top"><i class="fas fa-check-circle"></i></span>
                                              <?php endif ?>
                                            </p>
                                            <p class="leads mb-0 text-muted"><?php echo trans('booking-page') ?>: <a target="_blank" href="<?php echo base_url($user->company_slug) ?>"><?php echo ucfirst($user->currency_name); ?></a></p>
                                        </div>
                                      </div>
                                    </td>

                                     <td>
                                      <?php if ($user->status == 1): ?>
                                          <span class="badge-custom badge-success-soft"><i class="fas fa-check-circle"></i> <?php echo trans('active') ?></span>
                                      <?php else: ?>
                                        <span class="badge-custom badge-danger-soft"><i class="fas fa-times-circle"></i> <?php echo trans('disabled') ?></span>
                                      <?php endif ?>
                                    </td>
                                  
                                    <td> <?php echo html_escape(user_payment_details($user->id)->package); ?></td>
                               
                                    
                                    <td>
                                        <?php $label = ''; ?>
                                        <?php if (user_payment_details($user->id)->status == 'pending'){
                                          $label = 'secondary-soft';
                                          $text = '<i class="fas fa-clock"></i> '.trans(user_payment_details($user->id)->status);
                                        }else if(user_payment_details($user->id)->status == 'verified'){ 
                                          $label = 'success';
                                          $text = '<i class="fas fa-check"></i> '.trans(user_payment_details($user->id)->status);
                                        }else{ 
                                          $label = 'danger';
                                          $text = '<i class="fas fa-times"></i>'. trans('expired');
                                        }?>
                                        <span class="badge badge-<?php echo html_escape($label) ?>">
                                            <b><?= $text; ?></b>
                                        </span>
                                    </td>
                                    

                                    <td>
                                      <span class="mr-2 text-muted" data-toggle="tooltip" data-title="<?php echo trans('joined') ?>: <?php echo my_date_show($user->created_at) ?> " data-placement="top"><i class="fas fa-sign-in-alt"></i></span>

                                      <?php if ($user->payment_status != 'expire'): ?>
                                          <span class="text-muted ml-1" data-toggle="tooltip" data-title="<?php echo trans('expire') ?>: <?php echo date_dif(date('Y-m-d'), $user->payment->expire_on) ?> Days left" data-placement="top"><i class="fas fa-user-clock"></i></span>
                                      <?php else: ?>
                                          <span class="text-muted ml-1" data-toggle="tooltip" data-title="<?php echo trans('expire') ?>: <?php echo get_time_ago($user->payment->expire_on) ?>" data-placement="top"><i class="fas fa-user-clock text-danger"></i></span>
                                      <?php endif ?>
                                    </td>


                                    <td class="actions" width="12%">
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-tool" data-toggle="dropdown" aria-expanded="false">
                                              <i class="fas fa-ellipsis-h"></i>
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-right" role="menu" >
                                              <?php if (user_payment_details($user->id)->status != 'verified'): ?>
                                                <a data-toggle="modal" href="#recordModal_<?php echo html_escape($i) ?>" class="dropdown-item"><i class="lni lni-coin mr-1"></i> <?php echo trans('record-payment') ?></a>
                                              <?php else: ?>
                                                <a data-toggle="modal" href="#recordModal_<?php echo html_escape($i) ?>" class="dropdown-item"><i class="lnib lni-pencil mr-1"></i> <?php echo trans('update-plan') ?></a>
                                              <?php endif; ?>

                                              <?php if ($user->status == 1): ?>
                                                  <a href="<?php echo base_url('admin/users/status_action/2/'.html_escape($user->id));?>" class="dropdown-item"><i class="lnib lni-cross-circle mr-1"></i>  <?php echo trans('deactivate') ?></a>
                                              <?php else: ?>
                                                  <a href="<?php echo base_url('admin/users/status_action/1/'.html_escape($user->id));?>" class="dropdown-item"><i class="lnib lni-checkmark-circle mr-1"></i>  <?php echo trans('activate') ?></a>
                                              <?php endif ?>
                                              
                                              <a data-val="User" data-id="<?php echo html_escape($user->id); ?>" href="<?php echo base_url('admin/users/delete/'.html_escape($user->id));?>" class="dropdown-item delete_item"><i class="lni lni-trash-can mr-1"></i> <?php echo trans('delete') ?></a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            <?php $i++; endforeach ?>
                        </tbody>
                    </table>
                  <?php endif; ?>
                </div>
            </div>

            <div class="mt-4">
              <?php echo $this->pagination->create_links(); ?>
            </div>
          </div>
        </div>
          <!-- col-md-12 -->
        </div>
        <!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content -->
</div>







<?php $b=1; foreach ($users as $user): ?>
<div class="modal fade" id="recordModal_<?php echo html_escape($b) ?>">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header justify-content-between">
        <div><h4 class="modal-title"><?php echo trans('record-payment') ?></h4></div>
        <div class="mclose" data-dismiss="modal"><i class="lnib lni-close"></i></div>
      </div>

      <form method="post" enctype="multipart/form-data" action="<?php echo base_url('admin/payment/offline')?>" role="form" novalidate>
            
      <div class="modal-body">
          <div class="form-group mb-4">
              <label><?php echo trans('plans') ?></label>
              <select class="form-control single_select" name="package" required>
                  <option value=""><?php echo trans('select') ?></option>
                  <?php foreach ($packages as $package): ?>
                    <option value="<?php echo html_escape($package->id) ?>"><?php echo html_escape($package->name) ?> </option>
                  <?php endforeach ?>
              </select>
          </div>

          <div class="form-group mb-4">
              <label><?php echo trans('subscription-type') ?></label>
              <select class="form-control single_select" name="billing_type" required>
                  <option value=""><?php echo trans('select') ?></option>
                  <option value="monthly"><?php echo trans('monthly') ?></option>
                  <option value="yearly"><?php echo trans('yearly') ?></option>
              </select>
          </div>

          <div class="form-group mb-4">
              <label><?php echo trans('payment-status') ?></label>
              <select class="form-control single_select" name="status" required>
                  <option value=""><?php echo trans('select') ?></option>
                  <option value="verified"><?php echo trans('verified') ?></option>
                  <option value="pending"><?php echo trans('pending') ?></option>
              </select>
          </div>

      </div>

      <div class="modal-footer justify-content-between">
        <input type="hidden" name="user" value="<?php echo html_escape($user->id) ?>">
        <input type="hidden" name="<?php echo $this->security->get_csrf_token_name();?>" value="<?php echo $this->security->get_csrf_hash();?>">
        <button type="submit" class="btn btn-primary"><?php echo trans('add-payment') ?></button>
      </div>
      </form>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>
<?php $b++; endforeach; ?>