<!-- Top Bar Start -->
<?php echo fetch_view('top-bar', 'Public')?>
<!-- Top Bar End -->

<!--    col-lg-4 -->
<div class="">
    <div class="card-box">
        <div class="dropdown pull-right">
            <a href="#" class="dropdown-toggle card-drop" data-toggle="dropdown" aria-expanded="false">
                <i class="zmdi zmdi-more-vert"></i>
            </a>
            <ul class="dropdown-menu" role="menu">
                <li><a href="#">Action</a></li>
                <li><a href="#">Another action</a></li>
                <li><a href="#">Something else here</a></li>
                <li class="divider"></li>
                <li><a href="#">Separated link</a></li>
            </ul>
        </div>

        <h4 class="header-title m-t-0 m-b-30">Inbox</h4>

        <div class="inbox-widget nicescroll" style="height: 315px; overflow: hidden; outline: none;" tabindex="5000">
            <a href="#">
                <div class="inbox-item">
                    <div class="inbox-item-img"><img src="assets/images/users/avatar-1.jpg" class="img-circle" alt=""></div>
                    <p class="inbox-item-author">Chadengle</p>
                    <p class="inbox-item-text">Hey! there I'm available...</p>
                    <p class="inbox-item-date">13:40 PM</p>
                </div>
            </a>
            <a href="#">
                <div class="inbox-item">
                    <div class="inbox-item-img"><img src="assets/images/users/avatar-2.jpg" class="img-circle" alt=""></div>
                    <p class="inbox-item-author">Tomaslau</p>
                    <p class="inbox-item-text">I've finished it! See you so...</p>
                    <p class="inbox-item-date">13:34 PM</p>
                </div>
            </a>
            <a href="#">
                <div class="inbox-item">
                    <div class="inbox-item-img"><img src="assets/images/users/avatar-3.jpg" class="img-circle" alt=""></div>
                    <p class="inbox-item-author">Stillnotdavid</p>
                    <p class="inbox-item-text">This theme is awesome!</p>
                    <p class="inbox-item-date">13:17 PM</p>
                </div>
            </a>
            <a href="#">
                <div class="inbox-item">
                    <div class="inbox-item-img"><img src="assets/images/users/avatar-4.jpg" class="img-circle" alt=""></div>
                    <p class="inbox-item-author">Kurafire</p>
                    <p class="inbox-item-text">Nice to meet you</p>
                    <p class="inbox-item-date">12:20 PM</p>
                </div>
            </a>
            <a href="#">
                <div class="inbox-item">
                    <div class="inbox-item-img"><img src="assets/images/users/avatar-5.jpg" class="img-circle" alt=""></div>
                    <p class="inbox-item-author">Shahedk</p>
                    <p class="inbox-item-text">Hey! there I'm available...</p>
                    <p class="inbox-item-date">10:15 AM</p>
                </div>
            </a>
        </div>
    </div>
</div><!-- end col -->

<div class="col-lg-8" style="display: none">
    <div class="card-box">
        <div class="dropdown pull-right">
            <a href="#" class="dropdown-toggle card-drop" data-toggle="dropdown" aria-expanded="false">
                <i class="zmdi zmdi-more-vert"></i>
            </a>
            <ul class="dropdown-menu" role="menu">
                <li><a href="#">Action</a></li>
                <li><a href="#">Another action</a></li>
                <li><a href="#">Something else here</a></li>
                <li class="divider"></li>
                <li><a href="#">Separated link</a></li>
            </ul>
        </div>

        <h4 class="header-title m-t-0 m-b-30">Latest Projects</h4>

        <div class="table-responsive">
            <table class="table">
                <thead>
                <tr>
                    <th>#</th>
                    <th>Project Name</th>
                    <th>Start Date</th>
                    <th>Due Date</th>
                    <th>Status</th>
                    <th>Assign</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>1</td>
                    <td>Adminto Admin v1</td>
                    <td>01/01/2016</td>
                    <td>26/04/2016</td>
                    <td><span class="label label-danger">Released</span></td>
                    <td>Coderthemes</td>
                </tr>
                <tr>
                    <td>2</td>
                    <td>Adminto Frontend v1</td>
                    <td>01/01/2016</td>
                    <td>26/04/2016</td>
                    <td><span class="label label-success">Released</span></td>
                    <td>Adminto admin</td>
                </tr>
                <tr>
                    <td>3</td>
                    <td>Adminto Admin v1.1</td>
                    <td>01/05/2016</td>
                    <td>10/05/2016</td>
                    <td><span class="label label-pink">Pending</span></td>
                    <td>Coderthemes</td>
                </tr>
                <tr>
                    <td>4</td>
                    <td>Adminto Frontend v1.1</td>
                    <td>01/01/2016</td>
                    <td>31/05/2016</td>
                    <td><span class="label label-purple">Work in Progress</span>
                    </td>
                    <td>Adminto admin</td>
                </tr>
                <tr>
                    <td>5</td>
                    <td>Adminto Admin v1.3</td>
                    <td>01/01/2016</td>
                    <td>31/05/2016</td>
                    <td><span class="label label-warning">Coming soon</span></td>
                    <td>Coderthemes</td>
                </tr>

                <tr>
                    <td>6</td>
                    <td>Adminto Admin v1.3</td>
                    <td>01/01/2016</td>
                    <td>31/05/2016</td>
                    <td><span class="label label-primary">Coming soon</span></td>
                    <td>Adminto admin</td>
                </tr>

                <tr>
                    <td>7</td>
                    <td>Adminto Admin v1.3</td>
                    <td>01/01/2016</td>
                    <td>31/05/2016</td>
                    <td><span class="label label-primary">Coming soon</span></td>
                    <td>Adminto admin</td>
                </tr>

                </tbody>
            </table>
        </div>
    </div>
</div><!-- end col -->
