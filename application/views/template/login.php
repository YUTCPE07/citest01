<div class="modal fade" id="login" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" ng-controller="loginController">
    <!-- <div class="pt-5 mt-5" id="login" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true"> -->
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Login</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
               <input type="text" class="form-control">
               <br>
               <input type="text" class="form-control">
               <br>
               <div class="d-flex justify-content-center" >
               <img class="form-row img_login_facebook " src=" <?php echo base_url('assets\images\login\login_facebook.png') ?>"
                    ng-click="loginFacebook();">
                 
               </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" ng-click="logoutFacebook();" >logout</button>
            
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button type="button" class="btn btn-primary">Login</button>
          </div>
        </div>
      </div>
    </div>