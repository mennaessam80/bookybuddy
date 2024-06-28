<?php
include 'config.php';

session_start();

$user_id = $_SESSION['user_id'];

if (!isset($user_id)) {
    header('location:login.php');
    exit();
}

if (isset($_POST['view_exam_answer'])) {
    $exam_id = $_POST['exam_id'];
    $exam_name = $_POST['exam_name'];

    $exam_query = mysqli_query($conn, "SELECT answers, answers_premium FROM exams WHERE id = '$exam_id'") or die('query failed');
    $exam_data = mysqli_fetch_assoc($exam_query);

    $subscription_query = mysqli_query($conn, "SELECT payment_status FROM subscriptions WHERE user_id = '$user_id' AND payment_status = 'completed'") or die('query failed');
    $is_premium = mysqli_num_rows($subscription_query) > 0;

    if ($exam_data['answers_premium'] == 'yes' && !$is_premium) {
        $message[] = 'You need a premium subscription to view this answer!';
    } else {
        if (!empty($exam_data['answers'])) {
            header("Location: uploads/" . $exam_data['answers']);
        } else {
            $message[] = 'No PDF answer available for this exam.';
        }
        exit();
    }
}

// Handle filtering
$filter_grade = isset($_POST['grade']) ? $_POST['grade'] : '';
$filter_subject = isset($_POST['subject']) ? $_POST['subject'] : '';
$filter_answers_premium = isset($_POST['answers_premium']) ? $_POST['answers_premium'] : '';

$where_conditions = [];
if ($filter_grade != '') {
    $where_conditions[] = "grade = '$filter_grade'";
}
if ($filter_subject != '') {
    $where_conditions[] = "subject = '$filter_subject'";
}
if ($filter_answers_premium != '') {
    $where_conditions[] = "answers_premium = '$filter_answers_premium'";
}

$where_sql = '';
if (count($where_conditions) > 0) {
    $where_sql = "WHERE " . implode(' AND ', $where_conditions);
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
    <link rel="stylesheet" href="css/aa.css">
    <style>
    .sidebar {
        width: 250px;
        float: left;
        height: 100%;
        padding: 40px;
    }

    .sidebar h3 {
        margin-bottom: 20px;
        font-size: 1.8rem;
        color: #333;
    }

    .sidebar a {
        display: block;
        color: #555;
        text-decoration: none;
        margin-bottom: 10px;
        padding: 10px 15px;
        background: #fff;
        border-radius: 5px;
        transition: background 0.3s, color 0.3s;
        font-size: 1.5rem;
    }

    .sidebar a:hover {
        background: #e3e3e3;
        color: #000;
    }

    .sidebar .filter-group {
        margin-bottom: 30px;
    }

    .content {
        margin-left: 270px;
        padding: 20px;
    }
    </style>
</head>

<body>

    <?php include 'header.php'; ?>

    <div class="heading">
        <h3>Exams</h3>
        <p><a href="home.php">home</a> / exams</p>
    </div>

    <div class="sidebar">
        <form action="" method="post">
            <div class="filter-group">
                <h3>Filter by Grade</h3>
                <select name="grade" class="box">
                    <option value="">All Grades</option>
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
            </div>
            <div class="filter-group">
                <h3>Filter by Subject</h3>
                <select name="subject" id="subject" class="box">
                    <option value="">All Subjects</option>
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
            </div>
            <div class="filter-group">
                <h3>Do you want the premium Exam</h3>
                <select name="answers_premium" id="answers_premium" class="box">
                    <option value="">All</option>
                    <option value="no">لا</option>
                    <option value="yes">نعم</option>
                </select>
            </div>
            <button type="submit" class="btn">Filter</button><br>
            <a href="exa.php" class="btn" style=" background: #DDDAD1; width:70%; font-size: 1.5rem;">Reset Filter</a>
        </form>
    </div>

    <section class="products content">

        <h1 class="title">Latest Exams</h1>

        <div class="box-container">

            <?php  
        $select_exams = mysqli_query($conn, "SELECT * FROM `exams` $where_sql") or die('query failed');
        if (mysqli_num_rows($select_exams) > 0) {
            while ($fetch_exams = mysqli_fetch_assoc($select_exams)) {
                $exam_id = $fetch_exams['id'];
                $exam_name = $fetch_exams['name'];
                $answers_premium = $fetch_exams['answers_premium'];

                $subscription_query = mysqli_query($conn, "SELECT payment_status FROM subscriptions WHERE user_id = '$user_id' AND payment_status = 'completed'") or die('query failed');
                $is_premium = mysqli_num_rows($subscription_query) > 0;

                $button_color = ($answers_premium == 'yes' && !$is_premium) ? 'red' : 'green';
        ?>
            <form action="" method="post" class="box">
                <div class="name"><?php echo htmlspecialchars($exam_name); ?></div>
                <div class="details">Grade: <?php echo htmlspecialchars($fetch_exams['grade']); ?></div>
                <div class="details">Subject: <?php echo htmlspecialchars($fetch_exams['subject']); ?></div>
                <?php if (!empty($fetch_exams['pdf'])) { ?>
                <a href="uploads/<?php echo htmlspecialchars($fetch_exams['pdf']); ?>" class="btn"
                    style="background-color: #4CAF50;" download>Download PDF</a>
                <?php } elseif (!empty($fetch_exams['link'])) { ?>
                <a href="<?php echo htmlspecialchars($fetch_exams['link']); ?>" class="btn"
                    style="background-color: #4CAF50;">View Link</a>
                <?php } ?>
                <input type="hidden" name="exam_id" value="<?php echo $exam_id; ?>">
                <input type="hidden" name="exam_name" value="<?php echo htmlspecialchars($exam_name); ?>">

                <button type="submit" name="view_exam_answer" class="btn"
                    style="background-color: <?php echo $button_color; ?>;">View Exam Answer</button>

            </form>
            <?php
            }
        } else {
            echo '<p class="empty">No exams added yet!</p>';
        }
        ?>
        </div>
    </section>

    <script src="js/script.js"></script>
</body>

</html>