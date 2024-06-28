<?php
include 'config.php';
session_start();
$admin_id = $_SESSION['admin_id'];
if (!isset($admin_id)) {
    header('location:login.php');
}

if (isset($_POST['add_exam'])) {
    $name = mysqli_real_escape_string($conn, $_POST['exam_name']);
    $link = isset($_POST['exam_link']) ? mysqli_real_escape_string($conn, $_POST['exam_link']) : '';
    $pdf = isset($_FILES['exam_pdf']['name']) ? $_FILES['exam_pdf']['name'] : '';
    $grade = mysqli_real_escape_string($conn, $_POST['grade']);
    $subject = mysqli_real_escape_string($conn, $_POST['subject']);
    $answers_pdf = isset($_FILES['exam_answers_pdf']['name']) ? $_FILES['exam_answers_pdf']['name'] : '';
    $answers_premium = mysqli_real_escape_string($conn, $_POST['answers_premium']);

    $uploads_dir = 'uploads';
    if (!is_dir($uploads_dir)) {
        mkdir($uploads_dir, 0777, true);
    }

    $pdf_path = $pdf ? $uploads_dir . '/' . basename($pdf) : '';
    $answers_pdf_path = $answers_pdf ? $uploads_dir . '/' . basename($answers_pdf) : '';

    if ($link) {
        $select_exam = mysqli_query($conn, "SELECT link FROM `exams` WHERE link = '$link'") or die('query failed');
        if (mysqli_num_rows($select_exam) > 0) {
            $message[] = 'Exam link already exists';
        } else {
            if ($answers_pdf && move_uploaded_file($_FILES['exam_answers_pdf']['tmp_name'], $answers_pdf_path)) {
                $add_exam_query = mysqli_query($conn, "INSERT INTO `exams` (name, link, grade, subject, answers, answers_premium) VALUES ('$name', '$link', '$grade', '$subject', '$answers_pdf', '$answers_premium')") or die('query failed');
            } else {
                $add_exam_query = mysqli_query($conn, "INSERT INTO `exams` (name, link, grade, subject, answers_premium) VALUES ('$name', '$link', '$grade', '$subject', '$answers_premium')") or die('query failed');
            }
        }
    } elseif ($pdf) {
        if (move_uploaded_file($_FILES['exam_pdf']['tmp_name'], $pdf_path)) {
            if ($answers_pdf && move_uploaded_file($_FILES['exam_answers_pdf']['tmp_name'], $answers_pdf_path)) {
                $add_exam_query = mysqli_query($conn, "INSERT INTO `exams` (name, pdf, grade, subject, answers, answers_premium) VALUES ('$name', '$pdf', '$grade', '$subject', '$answers_pdf', '$answers_premium')") or die('query failed');
            } else {
                $add_exam_query = mysqli_query($conn, "INSERT INTO `exams` (name, pdf, grade, subject, answers_premium) VALUES ('$name', '$pdf', '$grade', '$subject', '$answers_premium')") or die('query failed');
            }
        } else {
            $message[] = 'Failed to upload PDF file';
        }
    }
}

if (isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];
    mysqli_query($conn, "DELETE FROM `exams` WHERE id = '$delete_id'") or die('query failed');
    header('location:admin_productsexa.php');
}

if (isset($_POST['update_exam'])) {
    $update_e_id = $_POST['update_e_id'];
    $update_name = mysqli_real_escape_string($conn, $_POST['update_name']);
    $update_link = isset($_POST['update_link']) ? mysqli_real_escape_string($conn, $_POST['update_link']) : '';
    $update_pdf = isset($_FILES['update_pdf']['name']) ? $_FILES['update_pdf']['name'] : '';
    $update_grade = mysqli_real_escape_string($conn, $_POST['update_grade']);
    $update_subject = mysqli_real_escape_string($conn, $_POST['update_subject']);
    $update_answers_pdf = isset($_FILES['update_answers_pdf']['name']) ? $_FILES['update_answers_pdf']['name'] : '';
    $update_answers_premium = mysqli_real_escape_string($conn, $_POST['update_answers_premium']);

    $uploads_dir = 'uploads';
    if (!is_dir($uploads_dir)) {
        mkdir($uploads_dir, 0777, true);
    }

    $pdf_path = $update_pdf ? $uploads_dir . '/' . basename($update_pdf) : '';
    $answers_pdf_path = $update_answers_pdf ? $uploads_dir . '/' . basename($update_answers_pdf) : '';

    if ($update_link) {
        mysqli_query($conn, "UPDATE `exams` SET name = '$update_name', link = '$update_link', pdf = NULL, answers = NULL, grade = '$update_grade', subject = '$update_subject', answers_premium = '$update_answers_premium' WHERE id = '$update_e_id'") or die('query failed');
    } elseif ($update_pdf) {
        if (move_uploaded_file($_FILES['update_pdf']['tmp_name'], $pdf_path)) {
            mysqli_query($conn, "UPDATE `exams` SET name = '$update_name', link = NULL, pdf = '$update_pdf', answers = NULL, grade = '$update_grade', subject = '$update_subject', answers_premium = '$update_answers_premium' WHERE id = '$update_e_id'") or die('query failed');
        } else {
            $message[] = 'Failed to upload PDF file';
        }
    }

    if ($update_answers_pdf) {
        if (move_uploaded_file($_FILES['update_answers_pdf']['tmp_name'], $answers_pdf_path)) {
            mysqli_query($conn, "UPDATE `exams` SET name = '$update_name', link = NULL, pdf = NULL, answers = '$update_answers_pdf', grade = '$update_grade', subject = '$update_subject', answers_premium = '$update_answers_premium' WHERE id = '$update_e_id'") or die('query failed');
        } else {
            $message[] = 'Failed to upload Answers PDF file';
        }
    }
    
    header('location:admin_productsexa.php');
}
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exams</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/admin_style.css">
</head>

<body>

    <?php include 'admin_header.php'; ?>

    <section class="add-products">
        <h1 class="title">Exams</h1>
        <form action="" method="post" enctype="multipart/form-data">
            <h3>Add Exam</h3>
            <label for="exam_name">اسم الامتحان:</label>
            <input type="text" id="exam_name" name="exam_name" class="box" required>
            <label for="exam_type">نوع الامتحان:</label>
            <select name="exam_type" id="exam_type" class="box" required onchange="toggleExamType()">
                <option value="link">رابط</option>
                <option value="pdf">PDF</option>
            </select>
            <div id="link_input">
                <label for="exam_link">رابط الامتحان:</label>
                <input type="text" id="exam_link" name="exam_link" class="box">
            </div>
            <div id="pdf_input" style="display: none;">
                <label for="exam_pdf">رفع PDF:</label>
                <input type="file" id="exam_pdf" name="exam_pdf" class="box">
            </div>
            <label for="grade">الصف:</label>
            <select name="grade" id="grade" class="box" required>
                <option value="kg1">كي جي 1</option>
                <option value="kg2">كي جي 2</option>
                <option value="1prim">1ابتدائي</option>
                <option value="2prim">2ابتدائي</option>
                <option value="3prim">3ابتدائي</option>
                <option value="4prim">4ابتدائي</option>
                <option value="5prim">5ابتدائي</option>
                <option value="6prim">6ابتدائي</option>
                <option value="1prep">1اعدادي</option>
                <option value="2prep">2اعدادي</option>
                <option value="3prep">3اعدادي</option>
                <option value="1sec">1ثانوي</option>
                <option value="2sec">2ثانوي</option>
                <option value="3sec">3ثانوي</option>
                <option value="other">اخري</option>
            </select>
            <label for="subject">الموضوع:</label>
            <select name="subject" id="subject" class="box" required>
                <option value="arabic">لغة عربية</option>
                <option value="english">لغة انجليزية</option>
                <option value="german">لغة المانية</option>
                <option value="french">لغة فرنسية</option>
                <option value="italian">لغة إيطالية</option>
                <option value="physics">فيزياء</option>
                <option value="chemistry">كيمياء</option>
                <option value="biology">أحياء</option>
                <option value="history">تاريخ</option>
                <option value="geography">جغرافيا</option>
                <option value="psychology">علم نفس</option>
                <option value="philosophy">فلسفة</option>
                <option value="math">رياضيات</option>
                <option value="geology">جيولوجيا</option>
                <option value="biology_en">Biology</option>
                <option value="physics_en">Physics</option>
                <option value="chemistry_en">Chemistry</option>
                <option value="math_en">Math</option>
                <option value="economics">الاقتصاد</option>
                <option value="pe">رياضة</option>
                <option value="other">اخري</option>
            </select>
            <label for="answers_premium">هل الاجابات للمستخدمين المميزين؟</label>
            <select name="answers_premium" id="answers_premium" class="box" required>
                <option value="no">لا</option>
                <option value="yes">نعم</option>
            </select>
            <label for="exam_answers_pdf">رفع اجابات PDF:</label>
            <input type="file" id="exam_answers_pdf" name="exam_answers_pdf" class="box" required>

            <input type="submit" value="Add Exam" name="add_exam" class="btn">
        </form>
    </section>

    <section class="show-products">
        <div class="box-container">
            <?php
        $select_exams = mysqli_query($conn, "SELECT * FROM `exams`") or die('query failed');
        if (mysqli_num_rows($select_exams) > 0) {
            while ($fetch_exams = mysqli_fetch_assoc($select_exams)) {
        ?>
            <div class="box">
                <div class="name"><?php echo $fetch_exams['name']; ?></div>
                <div class="link" style=" overflow: auto;">
                    <?php 
                    if ($fetch_exams['link']) {
                        echo '<a href="' . $fetch_exams['link'] . '" target="_blank">' . $fetch_exams['link'] . '</a>';
                    } else {
                        echo '<a href="uploads/' . $fetch_exams['pdf'] . '" target="_blank">' . $fetch_exams['pdf'] . '</a>';
                    }
                ?>
                </div>
                <div class="grade"><?php echo $fetch_exams['grade']; ?></div>
                <div class="subject"><?php echo $fetch_exams['subject']; ?></div>
                <div class="answers_premium">Premium Answers: <?= $fetch_exams['answers_premium']; ?></div>
                <div class="answers" style="overflow: auto;">
                    <?php
                    if ($fetch_exams['answers']) {
                        echo '<a href="uploads/' . $fetch_exams['answers'] . '" target="_blank">View Answers</a>';
                    } else {
                        echo 'No Answers PDF';
                    }
                ?>
                </div>
                <a href="admin_productsexa.php?update=<?php echo $fetch_exams['id']; ?>" class="option-btn">Update</a>
                <a href="admin_productsexa.php?delete=<?php echo $fetch_exams['id']; ?>" class="delete-btn"
                    onclick="return confirm('Delete this exam?');">Delete</a>
            </div>
            <?php
            }
        } else {
            echo '<p class="empty">No exams added yet!</p>';
        }
        ?>
        </div>
    </section>


    <section class="edit-product-form">
        <?php
        if (isset($_GET['update'])) {
            $update_id = $_GET['update'];
            $update_query = mysqli_query($conn, "SELECT * FROM `exams` WHERE id = '$update_id'") or die('query failed');
            if (mysqli_num_rows($update_query) > 0) {
                while ($fetch_update = mysqli_fetch_assoc($update_query)) {
        ?>
        <form action="" method="post" enctype="multipart/form-data">
            <input type="hidden" name="update_e_id" value="<?php echo $fetch_update['id']; ?>">
            <input type="text" name="update_name" value="<?php echo $fetch_update['name']; ?>" class="box" required>
            <select name="exam_type_update" id="exam_type_update" class="box" required
                onchange="toggleUpdateExamType()">
                <option value="link" <?php if ($fetch_update['link']) echo 'selected'; ?>>Link</option>
                <option value="pdf" <?php if ($fetch_update['pdf']) echo 'selected'; ?>>PDF</option>
            </select>
            <div id="link_input_update" style="<?php if (!$fetch_update['link']) echo 'display: none;'; ?>">
                <input type="text" name="update_link" value="<?php echo $fetch_update['link']; ?>" class="box">
            </div>
            <div id="pdf_input_update" style="<?php if (!$fetch_update['pdf']) echo 'display: none;'; ?>">
                <input type="file" name="update_pdf" class="box">
            </div>
            <input type="text" name="update_grade" value="<?php echo $fetch_update['grade']; ?>" class="box" required>
            <input type="text" name="update_subject" value="<?php echo $fetch_update['subject']; ?>" class="box"
                required>
            <label for="update_answers_pdf">رفع اجابات PDF:</label>
            <input type="file" name="update_answers_pdf" class="box" required>

            <?php echo $fetch_update['answers']; ?></textarea>
            <label for="update_answers_premium">هل الاجابات للمستخدمين المميزين؟</label>
            <select name="update_answers_premium" id="update_answers_premium" class="box" required>
                <option value="no" <?php if ($fetch_update['answers_premium'] == 'no') echo 'selected'; ?>>لا</option>
                <option value="yes" <?php if ($fetch_update['answers_premium'] == 'yes') echo 'selected'; ?>>نعم
                </option>
            </select>
            <input type="submit" value="Update" name="update_exam" class="btn">
            <input type="reset" value="Cancel" id="close-update" class="option-btn">
        </form>
        <?php
                }
            }
        } else {
            echo '<script>document.querySelector(".edit-product-form").style.display = "none";</script>';
        }
        ?>
    </section>

    <script>
    function toggleExamType() {
        var examType = document.getElementById('exam_type').value;
        if (examType === 'link') {
            document.getElementById('link_input').style.display = 'block';
            document.getElementById('pdf_input').style.display = 'none';
        } else {
            document.getElementById('link_input').style.display = 'none';
            document.getElementById('pdf_input').style.display = 'block';
        }
    }

    function toggleUpdateExamType() {
        var examType = document.getElementById('exam_type_update').value;
        if (examType === 'link') {
            document.getElementById('link_input_update').style.display = 'block';
            document.getElementById('pdf_input_update').style.display = 'none';
        } else {
            document.getElementById('link_input_update').style.display = 'none';
            document.getElementById('pdf_input_update').style.display = 'block';
        }
    }

    document.getElementById('close-update').addEventListener('click', function() {
        document.querySelector('.edit-product-form').style.display = 'none';
    });
    </script>

    <script src="js/admin_script.js"></script>

</body>

</html>