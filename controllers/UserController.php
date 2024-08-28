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

        if (Yii::$app->request->isPost && $model->load(Yii::$app->request->post())) {
            $model->file = UploadedFile::getInstance($model, 'file');
            if ($model->file && $model->validate()) {
                $uploadPath = Yii::getAlias('@webroot/uploads/'); // Change this to your desired path
                if (!is_dir($uploadPath)) {
                    mkdir($uploadPath, 0755, true); // Create the directory if it does not exist
                }
                $filePath = $uploadPath . $model->file->baseName . '.' . $model->file->extension;
                $model->file->saveAs($filePath);
                $spreadsheet = IOFactory::load($filePath);
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
                    $user->Date = $row['G'];
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