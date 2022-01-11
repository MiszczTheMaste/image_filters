<?php

namespace App;

require_once 'required.php';

use App\Entity\GaussMask;
use App\Entity\Image;
use App\Entity\RobertCross;
use App\Entity\SharpnessMask;
use App\Filter\AvarageFilter;
use App\Filter\BinarizationFilter;
use App\Filter\BrightnessFilter;
use App\Filter\ContrastFilter;
use App\Filter\GaussFilter;
use App\Filter\GrayScaleFilter;
use App\Filter\NegativeFilter;
use App\Filter\RobertFilter;
use App\Filter\SharpnessFilter;
use App\Histogram\HistogramGenerator;
/*






print_r($execution_time);
*/
?>


<!DOCTYPE html>
<html>


<head>
    <title>Filtracja Obrazów</title>
    <style>
        html,
        body {
            margin: 0;
            padding: 0;
            background-color: #1d1c19;
            color: #fff;
            font-family: Arial, sans-serif;
        }

        main {
            width: 100%;
        }

        main>div {
            width: 50%;
            float: left;
            text-align: center;
        }

        main>div img {
            width: 80%;
            height: auto;
        }

        main>div>p {
            font-size: 24px;
        }

        h1,
        h2 {
            text-align: center;
        }

        nav a {
            text-decoration: none;
            font-size: 32px;
            color: #fff;
        }

        nav a:hover {
            color: #aaaaff;
        }

        #uploadWrap {
            width: 100vw;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
        }

        #parametersWrapp {
            width: 100vw;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        #parametersWrapp input {
            text-align: right;
        }

        #uploadWrap input {
            border: 2px dashed;
            padding: 50px;
        }

        #fileUpload {

            font-size: 42px;
        }

        #nextButton {
            width: 100%;
            text-align: center !important;
            background-color: #202020;
            border: 2px solid #373737;
            color: #fff;
            padding: 10px;
            font-size: 14px;
        }

        #histogramLabel {
            font-size: 24px;
            margin-top: 50px;
            text-align: center;
        }

        #histogram {
            width: 100% !important;
        }
    </style>
</head>

<body>

    <?php

    if (isset($_GET['image'])) {

        if (isset($_GET['brightness'])) {
            $image = $_GET['image'];
            $brightness = $_GET['brightness'];
            $binarizationTreshold = $_GET['binarizationTreshold'];
            $avarageFilerMaskSize = $_GET['avarageFilerMaskSize'];
            $avarageFilerMaskWeight = $_GET['avarageFilerMaskWeight'];
            $gaussMaskSize = $_GET['gaussMaskSize'];
            $sharpnessMaskSize = $_GET['sharpnessMaskSize'];


            //
            //
            //
            //
            //mielenie
            //
            //
            //
            //

            $startTime = microtime(true);

            $img = new Image("input/" . $image . ".jpg");
            
            $grayScaleFilter = new GrayScaleFilter();
            $brightnessFilter = new BrightnessFilter();
            $brightnessFilter->setBrightnessValue($brightness);
            $contrastFilter = new ContrastFilter();
            $negativeFilter = new NegativeFilter();
            $binarizationFilter = new BinarizationFilter();
            $binarizationFilter->setTreshold($binarizationTreshold);
            $avarageFilter = new AvarageFilter();
            $avarageFilter->setMaskSize($avarageFilerMaskSize);
            $avarageFilter->setMastWeight($avarageFilerMaskWeight);
            $gaussFilter = new GaussFilter();
            $gaussFilter->setMask(new GaussMask($gaussMaskSize));
            $sharpnessFilter = new SharpnessFilter();
            $sharpnessFilter->setMask(new SharpnessMask($sharpnessMaskSize));
            $robertFilter = new RobertFilter();
            $robertFilter->setMask(new RobertCross());

            //te poprostu wyświetlić obraz
            $img->saveImage($grayScaleFilter->filter($img->getSrcImage()), $image . 'grayScale.png');
            $img->saveImage($brightnessFilter->filter($img->getSrcImage()), $image . 'brightness.png');
            $img->saveImage($contrastFilter->filter($img->getSrcImage()), $image . 'contrast.png');
            $img->saveImage($negativeFilter->filter($img->getSrcImage()), $image . 'negative.png');
            $img->saveImage($binarizationFilter->filter($img->getSrcImage()), $image . 'binarization.png');
            $img->saveImage($avarageFilter->filter($img->getSrcImage()), $image . 'avarage.png');
            $img->saveImage($gaussFilter->filter($img->getSrcImage()), $image . 'gauss.png');
            $img->saveImage($sharpnessFilter->filter($img->getSrcImage()), $image . 'sharpness.png');
            $img->saveImage($robertFilter->filter($img->getSrcImage()), $image . 'robertCross.png');

            $HistogramGenerator = new HistogramGenerator();
            $histogram = $HistogramGenerator->generate($img->getSrcImage());

            $endTime = microtime(true);
            $executionTime = round($endTime - $startTime, 2);
    ?>
            <script>
                let redHistogram = [];
                <?php
                foreach ($histogram['red'] as $row) {
                    echo "redHistogram.push({$row});";
                }
                ?>

                let greenHistogram = [];
                <?php
                foreach ($histogram['green'] as $row) {
                    echo "greenHistogram.push({$row});";
                }
                ?>

                let blueHistogram = [];
                <?php
                foreach ($histogram['blue'] as $row) {
                    echo "blueHistogram.push({$row});";
                }
                ?>
            </script>
            <main>
                <nav>
                    <a href="index.php">
                        << Powrót</a>
                </nav>
                <h1>Filtrowanie zakończone</h1>
                <h2>Czas pracy: <?= $executionTime ?>s</h2>
                <div>
                    <p>Oryginał</p>
                    <img src="input/<?= $image ?>.jpg">
                </div>
                <div>
                    <p>Skala szarości</p>
                    <a href="output/<?= $image ?>grayScale.png" download><img src="output/<?= $image ?>grayScale.png"></a>
                </div>
                <div>
                    <p>Jasność</p>
                    <a href="output/<?= $image ?>brightness.png" download><img src="output/<?= $image ?>brightness.png"></a>
                </div>
                <div>
                    <p>Kontrast</p>
                    <a href="output/<?= $image ?>contrast.png" download><img src="output/<?= $image ?>contrast.png"></a>
                </div>
                <div>
                    <p>Negatyw</p>
                    <a href="output/<?= $image ?>negative.png" download><img src="output/<?= $image ?>negative.png"></a>
                </div>
                <div>
                    <p>Binaryzacja</p>
                    <a href="output/<?= $image ?>binarization.png" download><img src="output/<?= $image ?>binarization.png"></a>
                </div>
                <div>
                    <p>Filt uśredniający</p>
                    <a href="output/<?= $image ?>avarage.png" download><img src="output/<?= $image ?>avarage.png"></a>
                </div>
                <div>
                    <p>Filtr Gaussa</p>
                    <a href="output/<?= $image ?>gauss.png" download><img src="output/<?= $image ?>gauss.png"></a>
                </div>
                <div>
                    <p>Wyostrzenie</p>
                    <a href="output/<?= $image ?>sharpness.png" download><img src="output/<?= $image ?>sharpness.png"></a>
                </div>
                <div>
                    <p>Krzyż Robertsa</p>
                    <a href="output/<?= $image ?>robertCross.png" download><img src="output/<?= $image ?>robertCross.png"></a>
                </div>


                <div id="histogram">
                    <p id="histogramLabel">Histogram ogryginalnego obrazu</p>
                    <canvas id="histogramChart" width="400" height="400"></canvas>
                </div>
            </main>
            <script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.0/dist/chart.min.js"></script>
            <script>
                const ctx = document.getElementById('histogramChart').getContext('2d');
                let labels = [];
                for (let i = 0; i < 256; i++) {
                    labels.push(i);
                }

                const myChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Red',
                            data: redHistogram,
                            fill: false,
                            borderColor: 'rgb(255, 0, 0)',
                            tension: 0.1
                        }, {
                            label: 'Green',
                            data: greenHistogram,
                            fill: false,
                            borderColor: 'rgb(0, 255, 0)',
                            tension: 0.1
                        }, {
                            label: 'Blue',
                            data: blueHistogram,
                            fill: false,
                            borderColor: 'rgb(0, 0, 255)',
                            tension: 0.1
                        }]
                    },
                    options: {
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            </script>

        <?php

        } else {
        ?>
            <div id="parametersWrapp">
                <form action="" method="GET">
                    <h1>Wybierz parametry filtrów</h1>
                    <input type="text" name="image" value="<?= $_GET['image'] ?>" hidden>
                    <p>Jasność <input type="number" min=0 max=255 name="brightness" value=50></p>
                    <p>Poziom binaryzacji <input type="number" min=0 max=255 name="binarizationTreshold" value=100></p>
                    <p>Filtr uśredniający wielkość maski <input type="number" min=3 step="2" name="avarageFilerMaskSize" value=3></p>
                    <p>Filtr uśredniający waga maski <input type="number" min=1 name="avarageFilerMaskWeight" value=1></p>
                    <p>Rozmycie gaussa wielkość maski <input type="number" min=3 step=2 name=" gaussMaskSize" value=5></p>
                    <p>Wartość wyostrzenia <input type="number" min=1 name="sharpnessMaskSize" value=9></p>
                    <p><input type="submit" value="Dalej" id="nextButton"></p>
                </form>
            </div>

        <?php
        }
    } else {
        ?>
        <div id="uploadWrap">
            <div>
                <div id="fileUpload">
                    <input type="file" name="fileToUpload" id="fileToUpload" onchange="upload()" />
                </div>
            </div>

        </div>
    <?php
    }
    ?>

    <script>
        let fileId;

        function upload() {
            const fileInput = document.querySelector('#fileToUpload');
            const formData = new FormData();

            formData.append('fileToUpload', fileInput.files[0]);

            const options = {
                method: 'POST',
                body: formData,
            };
            let status;
            fetch('fileUpload.php', options)
                .then(response => {
                    status = response.status;
                    response.json().then(body => {
                        let message = body.message;
                        let fileName = body.fileName;
                        if (status != 200) {
                            alert(message);
                        } else {
                            window.location.search += 'image=' + fileName;
                        }
                    });
                });
        }
    </script>

</body>

</html>