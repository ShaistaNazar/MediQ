<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\CityLabsRepository;
use App\Http\Requests\LabsRequest;
use App\UserTestReports;
use App\Jobs\Notifications;
use App\http\Services\NotificationService;
use Auth;

/*
 * LabsController controller exteds Controller and provides apis for Labs.
*/
class LabsController extends Controller
{
    protected $cityLabRepo;

    function __construct(CityLabsRepository $cityLabsRepository)
    {

        $this->cityLabRepo = $cityLabsRepository;
    }

    /*
     * get lab tests
    */
    public function labsList(LabsRequest $request)
    {

        $input = $request->all();
        $user = new \stdClass;
        $getList = $this->cityLabRepo->getLabsByCity();

        if (count($getList) > 0) {

            $ResponseCode = \Config::get('constants.response.ResponseCode_success');
            $ResponseMessage = __('labs.labs_found');
            $param = 'Labs';
            $values = $getList;
        } else {

            $ResponseCode = \Config::get('constants.response.ResponseCode_success');
            $ResponseMessage = __('labs.labs_found');
            $param = 'Labs';
            $values = new \stdClass();
        }

        return responseMsg($ResponseCode, $ResponseMessage, $param, $values);
    }

    /*
     * get user nearby labs
    */
    public function UserNearbyLabs(LabsRequest $request)
    {

        $validated = $request->validated();
        $input = $request->all();
        $data = ['lat' => $input['lat'], 'long' => $input['long'], 'lab_id' => $input['lab_id']];

        $nearByLabs = $this->cityLabRepo->getNearbyLabsMethod($data);

        if (count($nearByLabs) > 0) {

            $ResponseCode = \Config::get('constants.response.ResponseCode_success');

            $ResponseMessage = __('labs.near_labs_found');
            $param = 'NearbyLabs';
            $values = $nearByLabs;
        } else {

            $ReponseCode = \Config::get('constant.response.ResponseCode_fail');
            $ResponseMessage = __('labs.labs_not_found');
            $param = 'NearBYLabs';
            $values = new \stdClass();
        }
        return responseMsg($ResponseCode, $ResponseMessage, $param, $values);
    }

    /*
     * get test categories
    */
    public function getTestCategoryList(LabsRequest $request)
    {

        $validated = $request->validated();
        $input = $request->all();

        $testsCategories = $this->cityLabRepo->getTestCategories($request);
        foreach ($testsCategories as $key => $value) {
            $value->lab_name = $value->labs->lab_name;
            $value->lab_logo = $value->labs->logo;
            $value->test_category_name = $value->testCategory->category_name;
            $value->category_logo = $value->testCategory->logo;
            unset($value->labs);
            unset($value->testCategory);
        }

        if (count($testsCategories) > 0) {

            $ResponseCode = \Config::get('constants.response.ResponseCode_success');
            $ResponseMessage = __('labs.labs_tests_category_found');
            $param = 'lab tests';
            $values = $testsCategories;
        } else {

            $ResponseCode = \Config::get('constants.response.ResponseCode_success');
            $ResponseMessage = __('labs.labs_tests_category_found');
            $param = 'lab tests';
            $values = $testsCategories;
        }
        return responseMsg($ResponseCode, $ResponseMessage, $param, $values);
    }

    /*
     * get user's test reports
    */
    public function getTestUserLabReports(LabsRequest $request)
    {

        $input = $request->all();
        $validated = $request->validated();
        $reports = $this->cityLabRepo->getLabReports($input);

        if (count($reports) > 0) {

            $ResponseCode = \Config::get('constants.response.ResponseCode_success');

            $ResponseMessage = __('labs.labs_report_found');
            $param = 'lab reports';
            $values = $reports;
        } else {

            $ResponseCode = \Config::get('constant.response.ResponseCode_fail');
            $ResponseMessage = __('labs.labs_report_found');
            $param = 'lab reports';
            $values = new \stdClass();
        }
        return responseMsg($ResponseCode, $ResponseMessage, $param, $values);
    }

    /*
     * get lab tests
    */
    public function getTestList(LabsRequest $request)
    {

        $input = $request->all();
        $validated = $request->validated();

        $testList = $this->cityLabRepo->getTests($input);

        if (count($testList) > 0) {

            $ResponseCode = \Config::get('constants.response.ResponseCode_success');

            $ResponseMessage = __('labs.labs_tests_found');
            $param = 'lab reports';
            $values = $testList;
        } else {

            $ResponseCode = \Config::get('constants.response.ResponseCode_success');

            $ResponseMessage = __('labs.labs_tests_found');
            $param = 'lab reports';
            $values = $testList;
        }
        return responseMsg($ResponseCode, $ResponseMessage, $param, $values);
    }
    
    /*
     * Search lab tests
    */
    public function searchTest(LabsRequest $request)
    {

        $validated = $request->validated();
        $input = $request->all();
        $lab = isset($input['cat_id']);
        $searchTerm = $input['test_name'];
        $result = $this->cityLabRepo->searchTestMethod($searchTerm, $lab);

        if (count($result) > 0) {
            $ResponseCode = \Config::get('constants.response.ResponseCode_success');

            $ResponseMessage = __('labs.test_found');
            $param = 'Test';
            $values = $result;
        } else {
            $ResponseCode = \Config::get('constants.response.ResponseCode_success');

            $ResponseMessage = __('labs.test_found');
            $param = 'Test';
            $values = new \stdClass();
        }
        return responseMsg($ResponseCode, $ResponseMessage, $param, $values);
    }

    public function getUserTestReports(LabsRequest $request)
    {
        $offset = ($request['page'] * $request['limit']) - $request['limit'];
        $reports = UserTestReports::with('test')->where('user_id', Auth::id())->take($request['limit'])->skip($offset)->where('file', '!=', '')
            ->orderByDesc('created_at')->get();
        if ($reports)
            return responseMsg(\Config::get('constants.response.ResponseCode_success'), __('labs.test_reports'), 'report', $reports);
        return responseMsg(\Config::get('constants.response.ResponseCode_fail'), __('labs.reports_not_found'), 'report', new \stdClass());
    }
}
