<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
?>
<html>
<head>
    <script type="text/javascript" src="js/adapter.min.js"></script>
    <script type="text/javascript" src="js/vue.min.js"></script>
    <script type="text/javascript" src="js/instascan.min.js"></script>
    <link rel="stylesheet" href="css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <video id="preview" width="100%"></video>
                <?php
                if (isset($_SESSION['error'])) {
                    echo "
                    <div class='alert alert-danger'>
                        <h4>Error!</h4>
                        " . $_SESSION['error'] . "
                    </div>
                    ";
                    unset($_SESSION['error']); 
                }
                if (isset($_SESSION['success'])) {
                    echo "
                    <div class='alert alert-success' style='background:green;color:white;'>
                        <h4>Success!</h4>
                        " . $_SESSION['success'] . "
                    </div>
                    ";
                    unset($_SESSION['success']); 
                }
                ?>
            </div>

            <div class="col-md-6">
                <form action="insert1.php" method="post" class="form-horizontal">
                    <label>SCAN QR CODE</label>
                    <input type="text" name="text" id="text" readonly placeholder="Scan QR Code" class="form-control">
                </form>

                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>STUDENT ID</th>
                            <th>TIMEIN</th>
                            <th>TIMEOUT</th>
                            <th>LOGDATE</th>
                            <th>STATUS</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        date_default_timezone_set('Asia/Manila');
                        $server = "localhost";
                        $username = "root";
                        $password = "";
                        $dbname = "qrcodedb";

                        $conn = new mysqli($server, $username, $password, $dbname);
                        if ($conn->connect_error) {
                            die("Connection failed: " . $conn->connect_error);
                        }

                        $sql = "SELECT ID, STUDENTID, TIMEIN, TIMEOUT, LOGDATE, STATUS FROM table_attendance ORDER BY ID DESC";
                        $query = $conn->query($sql);

                        while ($row = $query->fetch_assoc()) {
                        ?>
                            <tr>
                                <td><?php echo $row["ID"]; ?></td>
                                <td><?php echo $row["STUDENTID"]; ?></td>
                                <td>
                                    <?php 
                                    echo ($row["TIMEIN"] == NULL || $row["TIMEIN"] == "0000-00-00 00:00:00") 
                                        ? "Not yet timed in" 
                                        : date("m/d/Y h:i:s A", strtotime($row["TIMEIN"])); 
                                    ?>
                                </td>
                                <td>
                                    <?php 
                                    echo ($row["TIMEOUT"] == NULL || $row["TIMEOUT"] == "0000-00-00 00:00:00") 
                                        ? "Not yet timed out" 
                                        : date("m/d/Y h:i:s A", strtotime($row["TIMEOUT"])); 
                                    ?>
                                </td>
                                <td><?php echo $row["LOGDATE"]; ?></td>
                                <td>
                                    <?php 
                                    if ($row["STATUS"] == 1) {
                                        echo "<span style='color: green;'>Timed Out</span>";
                                    } else {
                                        echo "<span style='color: red;'>Timed In</span>";
                                    }
                                    ?>
                                </td>
                            </tr>
                        <?php
                        }
                        $conn->close();
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
        <br>
        <button type="button" class="btn btn-success pull-right" onclick="Export()">
            <i class="fa fa-file-excel-o fa-fw"></i> Export to Excel
        </button>
    </div>

    <script>
        function Export() {
            var conf = confirm("Please confirm if you wish to proceed in exporting the attendance into an Excel File");
            if (conf == true) {
                window.open("export.php", '_blank');
            }
        }
    </script>

    <script>
        let scanner = new Instascan.Scanner({ video: document.getElementById('preview') });
        Instascan.Camera.getCameras().then(function (cameras) {
            if (cameras.length > 0) {
                scanner.start(cameras[0]);
            } else {
                alert('No cameras found');
            }
        }).catch(function (e) {
            console.error(e);
        });

        scanner.addListener('scan', function (c) {
            document.getElementById('text').value = c;
            document.forms[0].submit();
        });
    </script>
</body>
</html>
