<?php
require('../header.php');
require('../navbar.php');
?>

<div class="content">
  <div class="card" card-plain>
    <div class="card-header">
      <h4 class="card-title"> Simple Table</h4>
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
            <tr>
              <td class="text-center">
                2
              </td>
              <td>
                John Doe
              </td>
              <td>
                Design
              </td>
              <td class="text-center">
                2012
              </td>
              <td class="text-right">
                € 89,241
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
            <tr>
              <td class="text-center">
                3
              </td>
              <td>
                Alex Mike
              </td>
              <td>
                Design
              </td>
              <td class="text-center">
                2010
              </td>
              <td class="text-right">
                € 92,144
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
            <tr>
              <td class="text-center">
                4
              </td>
              <td>
                Mike Monday
              </td>
              <td>
                Marketing
              </td>
              <td class="text-center">
                2013
              </td>
              <td class="text-right">
                € 49,990
              </td>
              <td class="text-right">
                <button type="button" rel="tooltip" class="btn btn-info btn-icon btn-sm   btn-neutral  ">
                  <i class="fa fa-user"></i>
                </button>
                <button type="button" rel="tooltip" class="btn btn-success btn-icon btn-sm   btn-neutral  ">
                  <i class="fa fa-edit"></i>
                </button>
                <button type="button" rel="tooltip" class="btn btn-danger btn-icon btn-sm   btn-neutral  ">
                  <i class="fa fa-times"></i>
                </button>
              </td>
            </tr>
            <tr>
              <td class="text-center">
                5
              </td>
              <td>
                Paul Dickens
              </td>
              <td>
                Communication
              </td>
              <td class="text-center">
                2015
              </td>
              <td class="text-right">
                € 69,201
              </td>
              <td class="text-right">
                <button type="button" rel="tooltip" class="btn btn-info btn-icon btn-sm   btn-neutral  ">
                  <i class="fa fa-user"></i>
                </button>
                <button type="button" rel="tooltip" class="btn btn-success btn-icon btn-sm   btn-neutral  ">
                  <i class="fa fa-edit"></i>
                </button>
                <button type="button" rel="tooltip" class="btn btn-danger btn-icon btn-sm   btn-neutral  ">
                  <i class="fa fa-times"></i>
                </button>
              </td>
            </tr>
            <tr>
              <td class="text-center">
                6
              </td>
              <td>
                Manuel Rico
              </td>
              <td>
                Manager
              </td>
              <td class="text-center">
                2012
              </td>
              <td class="text-right">
                € 99,201
              </td>
              <td class="text-right">
                <button type="button" rel="tooltip" class="btn btn-info btn-icon btn-sm   btn-neutral  ">
                  <i class="fa fa-user"></i>
                </button>
                <button type="button" rel="tooltip" class="btn btn-success btn-icon btn-sm   btn-neutral  ">
                  <i class="fa fa-edit"></i>
                </button>
                <button type="button" rel="tooltip" class="btn btn-danger btn-icon btn-sm   btn-neutral  ">
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
