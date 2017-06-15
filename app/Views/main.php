<?php include_once 'header.php'?>
    <section id="main-content" style="min-height: 350px;">
        <div class="inner-content">
            <div class="card">
                <div class="card-header bg-primary">
                    <span class="card-title">Log File Viewer - Property Guru Offline Task</span>
                </div>
                <div class="card-content">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="search-form">
                                <div class="input-group">
                                    <input aria-describedby="pathHelp" id="lv-path" type="text" placeholder="/path/to/file" name="path" class="form-control input-lg">
                                    <div class="input-group-btn">
                                        <button class="btn btn-lg btn-primary" type="button" id="lv-load">
                                            View
                                        </button>
                                    </div>
                                </div>
                                <small id="pathHelp" class="form-text text-muted">
                                    Click to see demo : <b class="cursor-pointer" id="lv-demo">../storage/logs/test.log</b>
                                </small>
                            </div>
                            <div class="file-list float-e-margins">
                                <div class="file-list-content">
                                    <h2>
                                        Result for: <b class="text-navy" id="lv-path_name">"/path/to/file"</b>
                                    </h2>
                                    <small>Request time  (<b id="lv-timer">0.00</b> seconds)</small>
                                    <div class="hr-line-dashed"></div>

                                    <div id="lv-result">
                                        <div class="alert alert-warning">
                                            Pleas enter path
                                        </div>
                                    </div>

                                    <div class="text-center">
                                        <ul class="pagination">
                                            <li><a class="cursor-pointer" data-click="false" id="lv-begin"><i class="fa fa-angle-double-left fa-2x"></i></a></li>
                                            <li><a class="cursor-pointer" data-click="false" id="lv-prev"><i class="fa fa-angle-left fa-2x"></i></a></li>
                                            <li><a class="cursor-pointer" data-click="false" id="lv-next"><i class="fa fa-angle-right fa-2x"></i></a></li>
                                            <li><a class="cursor-pointer" data-click="false" id="lv-end"><i class="fa fa-angle-double-right fa-2x"></i></a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
<?php include_once 'footer.php'?>