<?php
include("../../../config/conn.php");
session_start();

$akses = $_SESSION['akses'];
$username = $_SESSION['username'];

if($akses != 'admin') {
    header('Location: ../..');
    exit();
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data and sanitize
    $nama_obat = htmlspecialchars($_POST['nama_obat']);
    $kemasan = htmlspecialchars($_POST['kemasan']);
    $harga = htmlspecialchars($_POST['harga']);

    // Call the function to insert dokter
    $updateMessage = insertObat ($nama_obat, $kemasan, $harga, $conn);

    if ($updateMessage == true) {
        $_SESSION['update_success'] = true;
        header("Location: " . $_SERVER["PHP_SELF"] . "?id=" . $id);
        exit();
    }
}

if (isset($_GET['delete'])) {
    // Get the ID of the poli to delete
    $id = $_GET['id'];

    // Call the deleteDokter function to delete the poli
    deleteObat($id, $conn); // Assuming $conn is your database connection
    echo "<script>alert('Obat berhasil dihapus.');</script>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Poliklinik</title>
    <link href="../../../src/styles.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .border {
            border: 1px solid black;
            border-radius: 5px;
        }
        .submit-button {
            background-color: #1E40AF; /* Default background color */
            border: none;
            color: white;
            font-weight: bold;
            padding: 10px 20px;
            border-radius: 5px;
            transition: background-color 0.3s ease-in-out, border-color 0.3s ease-in-out;
        }

        .submit-button:hover {
            background-color: #0E7490; /* Background color on hover */
            border-color: #0E7490; /* Border color on hover */
        }
        .reset-button {
            background-color: #D33715; /* Default background color */
            border: none;
            color: white;
            font-weight: bold;
            padding: 10px 20px;
            border-radius: 5px;
            transition: background-color 0.3s ease-in-out, border-color 0.3s ease-in-out;
        }

        .reset-button:hover {
            background-color: #FF4848; /* Background color on hover */
            border-color: #0E7490; /* Border color on hover */
        }
        .ubah-button {
            background-color: #ffc107; /* Default background color */
            border: none;
            color: white;
            font-weight: bold;
            padding: 10px 20px;
            border-radius: 5px;
            transition: background-color 0.3s ease-in-out, border-color 0.3s ease-in-out;
        }

        .ubah-button:hover {
            background-color: #FFD148; /* Background color on hover */
            border-color: #FFD148; /* Border color on hover */
        }
    </style>
</head>
<body>
        <?php include ("../../../src/components/sidebar.php") ?>
        <?php include ("../../../src/components/navbar-dashboard.php") ?>
    
        <div class="main-content p-5 flex-grow md:ml-64 transition-all">
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" class="w-full flex flex-col shadow-lg p-8">
                <h1 class="text-2xl font-bold">Kelola Obat</h1>
                <br>
                <div class="mb-4 flex flex-col">
                    <label for="nama_obat" class="block text-gray-700 font-bold mb-2 text-xl">Nama Obat</label>
                    <input type="text" id="nama_obat" name="nama_obat" class="form-input rounded-md border border-blue-900 p-2">
                </div>
                <div class="mb-4 flex flex-col">
                    <label for="kemasan" class="block text-gray-700 font-bold mb-2 text-xl">Kemasan</label>
                    <input type="text" id="kemasan" name="kemasan" class="form-input rounded-md border border-blue-900 p-2" >
                </div>
                <div class="mb-4 flex flex-col">
                    <label for="harga" class="block text-gray-700 font-bold mb-2 text-xl">Harga</label>
                    <input type="text" id="harga" name="harga" class="form-input rounded-md border border-blue-900 p-2" >
                </div>
                <div class="button-container">
                    <button type="submit" class="submit-button text-white font-bold py-2 px-4 rounded">
                        Submit
                    </button>
                    <button type="reset" class="reset-button text-white font-bold py-2 px-4 rounded">
                        Reset
                    </button>
                </div>
                <?php if (isset($_SESSION['update_success'])): ?>
                    <script>
                        alert("Data obat berhasil ditambahkan");
                        <?php unset($_SESSION['update_success']); // Clear the session variable ?>
                    </script>
                <?php endif; ?>
            </form>

            <h2 class="text-2xl font-bold my-10 text-center">Data Poli</h2>
            <div class="mt-8 p-8 shadow-lg flex flex-col text-center overflow-auto">
                <table class="table-auto ">
                    <thead class="border-b-2 border-black">
                        <tr>
                            <th class="px-4 py-2">No</th>
                            <th class="px-4 py-2">Obat</th>
                            <th class="px-4 py-2">Kemasan</th>
                            <th class="px-4 py-2">Harga</th>
                            <th class="px-4 py-2">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Query to fetch data
                        $sql = "SELECT * FROM obat";

                        // Prepare statement
                        $stmt = $conn->prepare($sql);

                        // Execute statement
                        $stmt->execute();

                        // Fetch all rows as an associative array
                        $obatList = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        $index = 1;
                        foreach ($obatList as $obat) { ?>
                            <tr class='text-center'>
                                <td class='px-4 py-2'> <?= ($index++); ?></td>
                                <td class='px-4 py-2'> <?= $obat['nama_obat']; ?></td>
                                <td class='px-4 py-2'> <?= $obat['kemasan']; ?></td>
                                <td class='px-4 py-2'>Rp. <?= $obat['harga']; ?></td>
                                <td class='px-4 py-2 flex justify-center w-full space-x-4'>
                                        <a class='ubah-button text-white font-bold rounded p-2' href='<?php echo str_replace("/obat.php", "", $_SERVER["PHP_SELF"]) . "../obat/update.php?id=" . $obat['id']; ?>'>Ubah</a>
                                        <form action='' method='GET' class=''>
                                            <input type='hidden' name='id' value='<?= $obat['id'];  ?>'>
                                            <button type='submit' name='delete' class='reset-button text-white font-bold rounded p-2'>Hapus</button>
                                        </form>
                                </td>
                            </tr>
                            <?php
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
</body>
</html>