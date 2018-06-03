<?php
require('../header.php');
require('../navbar.php');
?>

<div class="content">
  <div class="card card-plain">
    <div class="card-header">
      <h4 class="card-title"> Virtual Machine List</h4>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-striped">
          <thead class="text-primary">
            <th class="text-center">
              #
            </th>
            <th>
              Name
            </th>
            <th>
              Job Position
            </th>
            <th class="text-center">
              Since
            </th>
            <th class="text-right">
              Salary
            </th>
            <th class="text-right">
              Actions
            </th>
          </thead>
          <tbody>
            <tr>
              <td class="text-center">
                1
              </td>
              <td>
                Andrew Mike
              </td>
              <td>
                Develop
              </td>
              <td class="text-center">
                2013
              </td>
              <td class="text-right">
                € 99,225
              </td>
              <td class="text-right">
                <button type="button" rel="tooltip" class="btn btn-info btn-icon btn-sm ">
                  <i class="fa fa-user"></i>
                </button>
                <button type="button" rel="tooltip" class="btn btn-success btn-icon btn-sm ">
                  <i class="fa fa-edit"></i>
                </button>
                <button type="button" rel="tooltip" class="btn btn-danger btn-icon btn-sm ">
                  <i class="fa fa-times"></i>
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>



<?php
require('../footer.php');
?>
