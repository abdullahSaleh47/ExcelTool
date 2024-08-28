<?php
namespace app\models;

use yii\db\ActiveRecord;

/**
 * This is the model class for table "user".
 *
 * @property int $ID
 * @property int $UserID
 * @property string $First_Name
 * @property string $Last_Name
 * @property string $Gender
 * @property string $Country
 * @property int $Age
 * @property string $Date
 */
class EUser extends ActiveRecord
{
    const SCENARIO_CREATE='create';
    const SCENARIO_UPDATE='update';

    public static function tableName()
    {
        return 'User';
    }

    public function rules()
    {
        return [
            [['First_Name', 'Last_Name', 'Gender', 'Country', 'Age', 'Date','UserID'], 'required'],
            [['Age'], 'integer'],
            [['Date'], 'safe'],
            [['First_Name', 'Last_Name', 'Gender', 'Country'], 'string', 'max' => 255],
        ];
    }
    public function scenarios(){
        $scenarios=parent::scenarios();
        $scenarios['create']= ['First_Name','Last_Name','Gender','Age','Country','Date','UserID'];
        $scenarios['update']= ['First_Name','Last_Name','Gender','Age','Country','Date','UserID'];
        return $scenarios;
    }
    public function attributeLabels()
    {
        return [
            'ID' => 'ID',
            'First_name' => 'First Name',
            'Last_name' => 'Last Name',
            'Gender' => 'Gender',
            'Country' => 'Country',
            'Age' => 'Age',
            'Date' => 'Date',
            'UserID' => 'User ID',
        ];
    }
}
?>