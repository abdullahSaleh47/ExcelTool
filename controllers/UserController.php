<?php
namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\UploadedFile;
use PhpOffice\PhpSpreadsheet\IOFactory;
use app\models\EUser;

class UserController extends Controller
{
    public function actionUpload()
    {
        $model = new \yii\base\DynamicModel(['file']);
        $model->addRule('file', 'file', ['extensions' => 'xlsx, xls']);

        if (Yii::$app->request->isPost) {
            $model->file = UploadedFile::getInstance($model, 'file');
            if ($model->file && $model->validate()) {
                $spreadsheet = IOFactory::load($model->file->tempName);
                $worksheet = $spreadsheet->getActiveSheet();
                $data = $worksheet->toArray(null, true, true, true);

                foreach ($data as $row) {
                    if ($row['B'] == 'First Name') continue; // Skip header row

                    $user = new EUser();
                    $user->First_Name = $row['B'];
                    $user->Last_Name = $row['C'];
                    $user->Gender = $row['D'];
                    $user->Country = $row['E'];
                    $user->Age = $row['F'];
                   // Assume $row['G'] contains the Excel date value
                    $dateValue = $row['G'];
                    if (is_numeric($dateValue)) {
                        try {
                            // Convert Excel date to DateTime object
                            $dateTime = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($dateValue);

                            // Format DateTime object
                            $user->Date = $dateTime->format('d/m/Y');
                        } catch (\Exception $e) {
                            // Handle conversion error
                            Yii::error("Date conversion error: " . $e->getMessage());
                            $user->Date = null; // Set a default value or handle the error as needed
                        }
                    } else {
                        // Handle case where $dateValue is not numeric
                        Yii::error("Invalid date value: " . var_export($dateValue, true));
                        $user->Date = null; // Set a default value or handle the error as needed
                    }

                    $user->UserID=$row['H'];
                    $user->save();
                }
                Yii::$app->session->setFlash('success', 'File uploaded and data imported successfully!');
                return $this->redirect(['upload']);
            }
        }

        return $this->render('upload', ['model' => $model]);
    }
}

?>