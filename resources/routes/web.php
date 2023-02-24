<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\DeskController;
use App\Http\Controllers\GeneralController;
use App\Http\Controllers\SupportController;
use App\Http\Controllers\ExamofficerController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\AuditController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\MoppedUpController;
use App\Http\Controllers\StudentController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


/*
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');*/

Route::get('/clear', function() {
   $exitCode = Artisan::call('optimize:clear');
   $exitCode = Artisan::call('clear-compiled');
    return '<h1>Cache facade value cleared</h1>';
});
require __DIR__.'/auth.php';

//========================== report=======================
Route::post('registeredCourseToStudent', [ReportController::class,'postRegisteredCourseToStudent']);
Route::get('registeredCourseToStudent', [ReportController::class,'registeredCourseToStudent']);
Route::post('registeredCoursesToStudentsIII', [ReportController::class,'postRegisteredCoursesToStudentsIII']);
Route::post('registeredCoursesToStudentsII', [ReportController::class,'postRegisteredCoursesToStudentsII']);
Route::get('registeredCoursesToStudentsII', [ReportController::class,'registeredCoursesToStudentsII']);
Route::get('apiwithoutkey', [SupportController::class, 'apiWithoutKey'])->name('apiWithoutKey');
Route::get('asc', [SupportController::class, 'asc'])->name('asc');

Route::get('forget-password', [ForgotPasswordController::class, 'showForgetPasswordForm'])->name('forget.password.get');
Route::post('forget-password', [ForgotPasswordController::class, 'submitForgetPasswordForm'])->name('forget.password.post'); 
Route::get('reset-password/{token}', [ForgotPasswordController::class, 'showResetPasswordForm'])->name('reset.password.get');
Route::post('reset-password', [ForgotPasswordController::class, 'submitResetPasswordForm'])->name('reset.password.post');

Route::get('cleanRegisterCourseTable', [HomeController::class,'cleanRegisterCourseTable']);
//============== reverse grade =============================
Route::get('reverseGrade/{id}', [SupportController::class,'reverseGrade']);
Route::get('displayResultTable', [SupportController::class,'displayResultTable']);
//Route::get('updateR', [SupportController::class,'updateR']);
//Reoptimized class loader:
// change tfc to tms
//Route::get('autoAssignCourse', [SupportController::class,'autoAssignCourse']);
Route::get('updateCourse5', [SupportController::class,'updateCourse5']);
Route::post('studentImport', [SupportController::class,'studentImport']);
Route::get('updateCourse3', [SupportController::class,'updateCourse3']);
Route::get('updateCourse4/{id}', [SupportController::class,'updateCourse4']);
Route::get('updateCourse', [SupportController::class,'updateCourse']);
Route::get('updateRegisterCourseTable', [SupportController::class,'updateRegisterCourseTable']);
Route::get('changeTFCtoTMS', [SupportController::class,'changeTFCtoTMS']);
Route::get('getDuplicateUser', [SupportController::class,'getDuplicateUser']);
Route::get('getDuplicate', [SupportController::class,'getDuplicate']);
Route::get('getDuplicateCourse', [SupportController::class,'getDuplicateCourse']);
Route::post('getDuplicate', [SupportController::class,'postDuplicate']);
Route::get('/', [HomeController::class,'index']);
Route::get('changepassword', [HomeController::class,'changepassword']);
Route::post('changepassword', [HomeController::class,'post_changepassword']);
Route::post('updatedepartment', [HomeController::class,'updatedepartment']);
Route::post('updatedepartment2', [GeneralController::class,'updatedepartment']);
Route::get('getFosPara/{id}',[DeskController::class,'getFosPara']);
Route::get('changeemail', [HomeController::class,'changeemail']);
Route::post('changeemail', [HomeController::class,'post_changeemail']);
/*Route::get('password_reset', [Auth\ForgotPasswordController::class,'password_reset']);
Route::post('password_reset', [Auth\ForgotPasswordController::class,'post_password_reset']);
Route::get('password_reset/{token}', [Auth\ForgotPasswordController::class,'password_reset_token']);
Route::post('password_reset_token', [Auth\ForgotPasswordController::class,'post_password_reset_token']);*/
Route::get('getsemester/{id}',[ExamofficerController::class,'getsemester']);

Route::get('getlevel/{id}',[ExamofficerController::class,'getlevel']);
Route::get('getlevel/{id}',[ExamofficerController::class,'getlevel']);
Route::get('username/{id}',[HomeController::class,'username']);
Route::get('/depart/{id}', [HomeController::class,'getDepartment']);
Route::get('/depart_old/{id}', [HomeController::class,'getOldDepartment']);
Route::get('/fos_old/{id}', [HomeController::class,'getOldFos']);
Route::get('/fos/{id}', [HomeController::class,'getFos']);
Route::get('sfos/{id}', [HomeController::class,'Sfos']);
Route::get('sfos', [HomeController::class,'Sfos']);
Route::get('autocomplete_department',[HomeController::class,'autocomplete_department'])->name('autocomplete_department');
//==================================== General Controller =====================================================
// edit matric number
Route::get('edit_matric_number/{id}', [GeneralController::class,'edit_matric_number'])->middleware(['roles:admin,support,Deskofficer,,,']);
Route::post('edit_matric_number', [GeneralController::class,'post_edit_matric_number'])->middleware(['roles:admin,support,Deskofficer,,,']);
Route::post('updateMatricNo', [GeneralController::class,'updateMatricNo'])->middleware(['roles:admin,support,Deskofficer,examsofficer,,']);

// edit jamb Reg
Route::get('edit_jamb_reg/{id}', [GeneralController::class,'edit_jamb_reg'])->middleware(['roles:admin,support,Deskofficer,,,']);
Route::post('edit_jamb_reg', [GeneralController::class,'post_edit_jamb_reg'])->middleware(['roles:admin,support,Deskofficer,,,']);
//Route::post('updateMatricNo', [GeneralController::class,'updateMatricNo'])->middleware(['roles:admin,support,Deskofficer,examsofficer,,']);

// edit students profile
Route::get('edit_profile/{id}', [GeneralController::class,'edit_profile'])->middleware(['roles:admin,support,Deskofficer,,,']);
Route::post('edit_profile', [GeneralController::class,'post_edit_profile'])->middleware(['roles:admin,support,Deskofficer,,,']);

Route::get('resetStudentPassword/{id}', [GeneralController::class,'resetStudentPassword'])->middleware(['roles:admin,support,Deskofficer,,,']);

Route::get('uploadRightLog', [GeneralController::class,'uploadRightLog'])->middleware(['roles:support,,,,,']);
Route::get('regLog', [GeneralController::class,'regLog'])->middleware(['roles:support,,,,,']);
// ========================= support Controller=============================================================
Route::get('student_pin', [SupportController::class,'student_pin'])->middleware(['roles:support,Deskofficer,admin,,,']);
Route::get('get_student_pin', [SupportController::class,'get_student_pin'])->middleware(['roles:support,Deskofficer,admin,,,']);
Route::get('get_student_with_entry_year', [SupportController::class,'get_student_with_entry_year'])->middleware(['roles:support,,,,,']);
Route::get('create_pin', [SupportController::class,'get_create_pin'])->middleware(['roles:support,,,,']);
Route::post('create_pin', [SupportController::class,'post_create_pin'])->middleware(['roles:support,,,,']);
Route::get('view_unused_pin', [SupportController::class,'view_unused_pin'])->middleware(['roles:admin,support,,,']);
Route::get('view_used_pin', [SupportController::class,'view_used_pin'])->middleware(['roles:admin,support,Deskofficer,,,']);
Route::get('get_used_pin', [SupportController::class,'post_used_pin'])->middleware(['roles:admin,support,,,']);
Route::get('export_pin', [SupportController::class,'export_pin'])->middleware(['roles:support,,,,']);
Route::get('convert_pin', [SupportController::class,'convert_pin'])->middleware(['roles:admin,support,,,,']);
Route::post('convert_pin', [SupportController::class,'post_convert_pin'])->middleware(['roles:admin,support,,,,']);
//reset pin
Route::get('reset_pin', [SupportController::class,'reset_pin'])->middleware(['roles:admin,support,Deskofficer,,,']);
Route::post('reset_pin', [SupportController::class,'reset_pin'])->middleware(['roles:admin,support,Deskofficer,,,']);
Route::get('get_serial_number', [SupportController::class,'post_serial_number'])->middleware(['roles:admin,support,Deskofficer,,,']);
Route::get('individualResult', [SupportController::class,'individualResult'])->middleware(['roles:admin,support,dvc,,,']);
//================= register courses==================
Route::get('regCourse', [SupportController::class,'regCourse'])->middleware(['roles:admin,support,dvc,,,']);
Route::post('getRegCourse', [SupportController::class,'getRegCourse'])->middleware(['roles:admin,support,dvc,,,']);

//============================== HomeController======================================
//=========================== update email account for lecturers =====================
Route::get('update_email/{id}', [HomeController::class,'update_email']);
Route::post('update_email', [HomeController::class,'post_update_email']);
/*===================================contact ===============================================*/

Route::get('studentsWithOnlyProfile', [HomeController::class,'studentsWithOnlyProfile'])->middleware(['roles:admin,support,Deskofficer,,']);
Route::get('contactMail', [HomeController::class,'contactMail'])->middleware(['roles:admin,support,,,']);
Route::post('replyemail', [HomeController::class,'replyemail'])->middleware(['roles:admin,support,,,']);
//=============================== transfer year ======================================
Route::get('transfer_officer', [HomeController::class,'transfer_officer'])->middleware(['roles:admin,support,,,']);
Route::post('transfer_officer', [HomeController::class,'post_transfer_officer'])->middleware(['roles:admin,support,,,']);

//=============================== transfer lecturer ======================================
Route::get('transferLecturer', [HomeController::class,'transferLecturer'])->middleware(['roles:admin,support,,,']);
Route::post('transferLecturer', [HomeController::class,'post_transferLecturer'])->middleware(['roles:admin,support,,,']);
Route::get('get_transferLecturer', [HomeController::class,'get_transferLecturer'])->middleware(['roles:admin,support,,,']);
//====================== assign hod role========================================

Route::get('assign_hod_role', [HomeController::class,'assign_hod_role'])->middleware(['roles:admin,support,Deskofficer,,']);
Route::get('get_lecturer_4_hod', [HomeController::class,'get_lecturer_4_hod'])->middleware(['roles:admin,support,Deskofficer,,']);

Route::post('assign_hod', [HomeController::class,'assign_hod'])->middleware(['roles:admin,support,Deskofficer,,']);
Route::get('view_assign_hod', [HomeController::class,'view_assign_hod'])->middleware(['roles:admin,support,Deskofficer,,']);
Route::get('remove_hod/{id}', [HomeController::class,'remove_hod'])->middleware(['roles:admin,support,Deskofficer,,']);
//=================================== assign exams officer role =======================================
Route::get('assign_exams_officer', [HomeController::class,'assign_exams_officer'])->middleware(['roles:admin,support,Deskofficer,,']);
Route::get('get_lecturer_4_exams_officer', [HomeController::class,'get_lecturer_4_exams_officer'])->middleware(['roles:admin,support,Deskofficer,,']);
Route::post('assign_exams_officer', [HomeController::class,'assign_exams_officer'])->middleware(['roles:admin,support,Deskofficer,,']);
Route::get('view_assign_exams_officer', [HomeController::class,'view_exams_officer'])->middleware(['roles:admin,support,Deskofficer,,']);
Route::get('remove_exams_officer/{id}', [HomeController::class,'remove_exams_officer'])->middleware(['roles:admin,support,Deskofficer,,']);
Route::get('detail_exams_officer/{id}', [HomeController::class,'detail_exams_officer'])->middleware(['roles:admin,support,Deskofficer,,']);
Route::get('remove_fos/{id}', [HomeController::class,'remove_fos'])->middleware(['roles:admin,support,,,']);

/*===================================student detail ===============================================*/
Route::get('admin_studentdetails', [HomeController::class,'admin_studentdetails'])->middleware(['roles:admin,support,Deskofficer,,,']);
// edit images
Route::get('edit_image/{id}', [HomeController::class,'edit_image'])->middleware(['roles:admin,support,,,']);
Route::post('edit_image', [HomeController::class,'post_edit_image'])->middleware(['roles:admin,support,,,']);

/* ===============================================admin====================================================*/
//get number of registered Students
Route::get('admin_getRegStudents', [HomeController::class,'admin_getRegStudents'])->middleware(['roles:admin,support,DVC,,']);
Route::post('admin_getRegStudents', [HomeController::class,'post_getRegStudents'])->middleware(['roles:admin,support,DVC,,']);
//get registered Students
Route::get('admin_courseRegStudents', [HomeController::class,'admin_courseRegStudents']);
Route::post('admin_courseRegStudents', [HomeController::class,'post_courseRegStudents']);

//delete  registered Students
Route::get('delete_courseRegStudents/{id}', [HomeController::class,'delete_courseRegStudents'])->middleware(['roles:admin,support,,,']);
Route::post('delete_multiple_courseRegStudents', [HomeController::class,'delete_multiple_courseRegStudents'])->middleware(['roles:admin,support,,,']);
// view student
Route::get('admin_viewStudents', [ HomeController::class,'admin_viewStudents'])->middleware(['roles:admin,support,,,']);
Route::post('admin_viewStudents', [ HomeController::class,'post_viewStudents'])->middleware(['roles:admin,support,,,']);
Route::post('deleteStudents', [ HomeController::class,'deleteStudents'])->middleware(['roles:admin,support,,,']);
// faculty
Route::get('new_faculty', [HomeController::class,'new_faculty'])->middleware(['roles:admin,support,,,']);
Route::post('new_faculty', [HomeController::class,'post_new_faculty'])->middleware(['roles:admin,support,,,']);
Route::get('view_faculty', [HomeController::class,'view_faculty'])->middleware(['roles:admin,support,,,']);
Route::get('edit_faculty/{id}', [HomeController::class,'edit_faculty'])->middleware(['roles:admin,support,,,']);
Route::post('post_edit_faculty', [HomeController::class,'post_edit_faculty'])->middleware(['roles:admin,support,,,']);
//---------------------------------------------------------------------------------------------------
// department
Route::get('new_department', [HomeController::class,'new_department'])->middleware(['roles:admin,support,,,']);
Route::post('new_department', [HomeController::class,'post_new_department'])->middleware(['roles:admin,support,,,']);
Route::get('view_department', [HomeController::class,'view_department'])->middleware(['roles:admin,support,,,']);
Route::post('view_department', [HomeController::class,'post_view_department'])->middleware(['roles:admin,support,,,']);
Route::get('edit_department/{id}', [HomeController::class,'edit_department'])->middleware(['roles:admin,support,,,']);
Route::post('post_edit_department', [HomeController::class,'post_edit_department'])->middleware(['roles:admin,support,,,']);
//-----------------------------------------------------------------------------------------------------------------
//programme
Route::get('new_programme', [ HomeController::class,'new_programme'])->middleware(['roles:admin,support,,,']);
Route::post('new_programme', [ HomeController::class,'post_new_programme'])->middleware(['roles:admin,support,,,']);
Route::get('view_programme', [ HomeController::class,'view_programme'])->middleware(['roles:admin,support,,,']);

//-------------------------------------------------------------------------------------------------------------------
//fos
Route::get('new_fos', [ HomeController::class,'new_fos'])->middleware(['roles:admin,support,,,']);
Route::post('new_fos', [ HomeController::class,'post_new_fos'])->middleware(['roles:admin,support,,,']);
Route::get('view_fos', [ HomeController::class,'view_fos'])->middleware(['roles:admin,support,,,']);
Route::post('view_fos', [ HomeController::class,'post_view_fos'])->middleware(['roles:admin,support,,,']);

Route::get('edit_fos/{id}', [ HomeController::class,'edit_fos'])->middleware(['roles:admin,support,,,']);
Route::post('edit_fos', [ HomeController::class,'post_edit_fos'])->middleware(['roles:admin,support,,,']);
Route::post('assign_fos', [ HomeController::class,'post_assign_fos'])->middleware(['roles:admin,support,,,']);
Route::post('assign_fosdesk', [ HomeController::class,'assign_fosdesk'])->middleware(['roles:admin,support,,,']);
Route::get('assign_fos', [ HomeController::class,'assign_fos'])->middleware(['roles:admin,support,,,']);

Route::get('delete_fos/{id}/{yes?}', [ HomeController::class,'delete_fos'])->middleware(['roles:admin,support,,,']);
//==================================== specialization ================================
Route::get('newSpecialization', [ HomeController::class,'newSpecialization'])->middleware(['roles:admin,support,Deskofficer,,,']);
Route::post('newSpecialization', [ HomeController::class,'postSpecialization'])->middleware(['roles:admin,support,Deskofficer,,,']);
Route::get('viewSpecialization', [ HomeController::class,'viewSpecialization'])->middleware(['roles:admin,support,Deskofficer,,,']);
Route::post('viewSpecialization', [ HomeController::class,'postViewSpecialization'])->middleware(['roles:admin,support,Deskofficer,,,']);

Route::get('editSpecialization/{id}', [ HomeController::class,'editSpecialization'])->middleware(['roles:admin,support,Deskofficer,,,']);
Route::post('editSpecialization', [ HomeController::class,'updateSpecialization'])->middleware(['roles:admin,support,Deskofficer,,,']);
Route::post('assignSpecialization', [ HomeController::class,'postAssignSpecialization'])->middleware(['roles:admin,support,Deskofficer,,,']);
Route::post('updateAssignSpecialization', [ HomeController::class,'updateAssignSpecialization'])->middleware(['roles:admin,support,Deskofficer,,,']);
Route::get('assignSpecialization', [ HomeController::class,'assignSpecialization'])->middleware(['roles:admin,support,Deskofficer,,,']);
Route::get('viewAssignSpecialization', [ HomeController::class,'viewAssignSpecialization'])->middleware(['roles:admin,support,Deskofficer,,,']);
Route::post('viewAssignSpecialization', [ HomeController::class,'postViewAssignSpecialization'])->middleware(['roles:admin,support,Deskofficer,,,']);

//---------------------------------------------------------------------------------------------------
//class attendance
Route::get('admin_classAttendance', [SupportController::class,'admin_classAttendance']);//->middleware(['roles:support,Deskofficer,admin,,,']);

//-------------------------------------------------------------------------------------------------------------------
//desk officer
Route::get('new_desk_officer', [ HomeController::class,'new_desk_officer'])->middleware(['roles:admin,support,,,']);
Route::post('new_desk_officer', [ HomeController::class,'post_desk_officer'])->middleware(['roles:admin,support,,,']);
Route::get('view_desk_officer', [ HomeController::class,'view_desk_officer'])->middleware(['roles:admin,support,,,']);
Route::get('suspend_desk_officer', [ HomeController::class,'suspend_desk_officer'])->middleware(['roles:admin,support,,,']);

Route::get('/edit_right/{id}/{e}', [ HomeController::class,'edit_right'])->middleware(['roles:admin,support,Deskofficer,,,']);


Route::get('/activate/{id}/{e}', [ HomeController::class,'activate'])->middleware(['roles:admin,support,,,']);

Route::get('/suspend/{id}/{e?}', [ HomeController::class,'suspend'])->middleware(['roles:admin,support,,,']);
Route::get('/assign_deskofficer/{e}', [ HomeController::class,'assign_Deskofficer,,,,'])->middleware(['roles:admin,support,,,']);
Route::post('assign_deskofficer', [ HomeController::class,'post_assign_Deskofficer,,,,'])->middleware(['roles:admin,support,,,']);
Route::get('resultReport/{id}/{d}', [ HomeController::class,'resultReport'])->middleware(['roles:support,,,,']);
Route::get('resultReport1/{id}/{s}/{l}', [ HomeController::class,'resultReport2']);


//----------------------------------------------------------------------------------------------------------------
// predegree  create officer 
Route::get('pds_new_desk_officer', [ HomeController::class,'pds_new_desk_officer'])->middleware(['roles:admin,support,,,']);
Route::post('pds_new_desk_officer', [ HomeController::class,'pds_post_desk_officer'])->middleware(['roles:admin,support,,,']);
Route::get('pds_view_desk_officer', [ HomeController::class,'pds_view_desk_officer'])->middleware(['roles:admin,support,,,']);

// predegree create course
Route::get('pds_create_course', [ HomeController::class,'pds_create_course'])->middleware(['roles:admin,support,,,']);
Route::post('pds_create_course', [ HomeController::class,'pds_post_create_course'])->middleware(['roles:admin,support,,,']);
Route::get('pds_view_course', [ HomeController::class,'pds_view_course'])->middleware(['roles:admin,support,,,']);
Route::get('modern_view_course', [ HomeController::class,'modern_view_course'])->middleware(['roles:admin,support,,,']);
// create course unit
Route::get('create_course_unit', [ HomeController::class,'create_course_unit'])->middleware(['roles:admin,support,,,']);
Route::post('create_course_unit', [ HomeController::class,'post_create_course_unit'])->middleware(['roles:admin,support,,,']);
Route::get('create_course_unit_special', [ HomeController::class,'create_course_unit_special'])->middleware(['roles:admin,support,Deskofficer,,,']);
Route::post('create_course_unit_special', [ HomeController::class,'post_create_course_unit_special'])->middleware(['roles:admin,support,Deskofficer,,,']);
Route::get('view_course_unit', [ HomeController::class,'view_course_unit'])->middleware(['roles:admin,support,Deskofficer,,,']);
Route::post('view_course_unit', [ HomeController::class,'post_view_course_unit'])->middleware(['roles:admin,support,Deskofficer,,,']);
Route::get('adminreg_course', [ HomeController::class,'adminreg_course'])->middleware(['roles:admin,support,DVC,HOD,examsofficer,']);
Route::get('get_adminreg_course', [ HomeController::class,'post_adminreg_course'])->middleware(['roles:admin,support,DVC,HOD,examsofficer,']);
Route::get('delete_adminreg_course/{id}/{s}/{yes?}', [ HomeController::class,'delete_adminreg_course'])->middleware(['roles:admin,support,Deskofficer,,,']);
Route::post('delete_adminreg_multiple_course', [ HomeController::class,'delete_adminreg_multiple_course'])->middleware(['roles:admin,support,Deskofficer,,,']);
// edit course registration 
Route::get('edit_adminreg_course_semester/{id}/{s}', [ HomeController::class,'edit_adminreg_course_semester'])->middleware(['roles:admin,support,Deskofficer,,,']);
Route::post('edit_adminreg_course_semester', [ HomeController::class,'update_adminreg_course_semester'])->middleware(['roles:admin,support,Deskofficer,,,']);

Route::get('edit_adminreg_course/{id}/{s}', [ HomeController::class,'edit_adminreg_course'])->middleware(['roles:admin,support,Deskofficer,,,']);
Route::post('edit_adminreg_course', [ HomeController::class,'update_adminreg_course'])->middleware(['roles:admin,support,Deskofficer,,,']);
// edit of registration
Route::get('deleteRegistration/{id}', [ HomeController::class,'deleteRegistration'])->middleware(['roles:admin,support,Deskofficer,,,']);
// edit_course_unit
Route::get('edit_course_unit/{id}', [ HomeController::class,'edit_course_unit'])->middleware(['roles:admin,support,Deskofficer,,,']);
Route::post('update_course_unit', [ HomeController::class,'update_course_unit'])->middleware(['roles:admin,support,Deskofficer,,,']);

Route::get('classAttendance/{id}/{s}/{d}/{fos}/{semester}', [ HomeController::class,'classAttendance']);
Route::get('mopUpClassAttendance/{id}/{s}/{d}/{fos}/{l}/{semester}', [ MoppedUpController::class,'mopUpClassAttendance']);
Route::get('classAttendanceSoft/{id}/{s}/{d}/{fos}/{semester}', [ HomeController::class,'classAttendanceSoft']);

//-----------       add course to students ---------------------------------
Route::get('add_adminreg_course/{id}/{s}/{yes?}', [ HomeController::class,'add_adminreg_course'])->middleware(['roles:admin,support,Deskofficer,,,']);


//================================================Desk Officer ============================================

// lecturer
Route::get('new_lecturer', [ DeskController::class,'new_lecturer'])->middleware(['roles:Deskofficer,,,,']);
Route::post('new_lecturer', [DeskController::class,'post_new_lecturer'])->middleware(['roles:Deskofficer,,,,']);
Route::get('edit_lecturer/{id}', [DeskController::class,'edit_lecturer'])->middleware(['roles:Deskofficer,,,,']);
Route::post('edit_lecturer/{id}', [ DeskController::class,'post_edit_lecturer'])->middleware(['roles:Deskofficer,,,,']);
Route::get('view_lecturer', [ DeskController::class,'view_lecturer'])->middleware(['roles:Deskofficer,,,,']);
Route::get('print_lecturer', [ DeskController::class,'print_lecturer'])->middleware(['roles:Deskofficer,,,,']);
// create course
Route::get('new_course', [ DeskController::class,'new_course'])->middleware(['roles:Deskofficer,,,,']);
Route::post('new_course', [ DeskController::class,'post_new_course'])->middleware(['roles:Deskofficer,,,,']);
Route::get('view_course', [ DeskController::class,'view_course'])->middleware(['roles:Deskofficer,,,,']);
Route::post('view_course', [ DeskController::class,'get_view_course'])->middleware(['roles:Deskofficer,,,,']);
// reset passeord
Route::get('resetPassword/{id}', [ DeskController::class,'resetPassword'])->middleware(['roles:Deskofficer,,,,']);
// assign courses
Route::get('assign_course', [ DeskController::class,'assign_course'])->middleware(['roles:Deskofficer,admin,support,HOD,,']);
Route::post('get_assign_course', [ DeskController::class,'get_assign_course'])->middleware(['roles:Deskofficer,admin,support,HOD,,']);

Route::get('assign_course_other', [ DeskController::class,'assign_course_other'])->middleware(['roles:Deskofficer,admin,support,HOD,,']);
Route::post('assign_course_other', [ DeskController::class,'post_assign_course_other'])->middleware(['roles:Deskofficer,admin,support,HOD,,']);

Route::get('getLecturer/{id}', [ DeskController::class,'getLecturer'])->middleware(['roles:Deskofficer,admin,support,HOD,,']);

Route::post('assign_course', [ DeskController::class,'post_assign_course'])->middleware(['roles:Deskofficer,admin,support,HOD,,']);

Route::post('assign_course_o', [ DeskController::class,'post_assign_course_o'])->middleware(['roles:Deskofficer,admin,support,HOD,,']);
Route::get('view_assign_course', [ DeskController::class,'view_assign_course'])->middleware(['roles:Deskofficer,admin,support,DVC,HOD,']);
Route::post('view_assign_course', [ DeskController::class,'get_view_assign_course'])->middleware(['roles:Deskofficer,admin,support,DVC,HOD,']);
Route::get('print_assign_course', [ DeskController::class,'print_assign_course'])->middleware(['roles:Deskofficer,,,,']);
Route::post('print_assign_course', [ DeskController::class,'get_print_assign_course'])->middleware(['roles:Deskofficer,,,,']);
Route::get('remove_assign_course/{id}', [ DeskController::class,'remove_assign_course'])->middleware(['roles:Deskofficer,admin,support,HOD,,']);
Route::post('delete_multiple_course', [ DeskController::class,'delete_multiple_course'])->middleware(['roles:Deskofficer,,,,']);
Route::post('remove_multiple_assign_course', [ DeskController::class,'remove_multiple_assign_course'])->middleware(['roles:Deskofficer,admin,support,,,']);

// register courses
Route::get('register_course', [ DeskController::class,'register_course'])->middleware(['roles:Deskofficer,examsofficer,,,']);
Route::post('register_course', [ DeskController::class,'get_register_course'])->middleware(['roles:Deskofficer,examsofficer,,,']);
Route::post('reg_course', [ DeskController::class,'post_register_course'])->middleware(['roles:Deskofficer,examsofficer,,,']);
Route::get('view_register_course', [ DeskController::class,'view_register_course'])->middleware(['roles:Deskofficer,examsofficer,,,']);
Route::post('view_register_course', [ DeskController::class,'post_view_register_course'])->middleware(['roles:Deskofficer,examsofficer,,,']);
//  registered course  
Route::get('registeredcourse', [ DeskController::class,'registeredcourse'])->middleware(['roles:Deskofficer,HOD,examsofficer,,']);
Route::get('get_registeredcourse', [ DeskController::class,'post_registeredcourse'])->middleware(['roles:Deskofficer,HOD,examsofficer,,']);

Route::post('delete_desk_multiple_course', [ DeskController::class,'delete_desk_multiple_course'])->middleware(['roles:Deskofficer,,,,']);

// edit courses
Route::get('edit_course/{id}', [ DeskController::class,'edit_course'])->middleware(['roles:Deskofficer,,,,']);
Route::post('edit_course/{id}', [ DeskController::class,'post_edit_course'])->middleware(['roles:Deskofficer,,,,']);
// delete course
Route::get('delete_course/{id}', [ DeskController::class,'delete_course'])->middleware(['roles:Deskofficer,,,,']);

// specialization
Route::get('viewSpecializedCourse', [ DeskController::class,'viewSpecializedCourse'])->middleware(['roles:Deskofficer,,,,']);
Route::get('getSpecializedCourse', [ DeskController::class,'postSpecializedCourse'])->middleware(['roles:Deskofficer,,,,']);
Route::get('specialized_course', [ DeskController::class,'specialized_course'])->middleware(['roles:Deskofficer,,,,']);
Route::post('specialized_course', [ DeskController::class,'get_specialized_course'])->middleware(['roles:Deskofficer,,,,']);
Route::post('reg_specialized_course', [ DeskController::class,'post_specialized_course'])->middleware(['roles:Deskofficer,,,,']);
Route::get('view_specialized_course', [ DeskController::class,'view_specialized_course'])->middleware(['roles:Deskofficer,,,,']);
Route::post('view_specialized_course', [ DeskController::class,'post_view_specialized_course'])->middleware(['roles:Deskofficer,,,,']);

// student
Route::get('new_student', [ DeskController::class,'new_student'])->middleware(['roles:Deskofficer,,,,']);
Route::post('new_student', [ DeskController::class,'post_new_student'])->middleware(['roles:Deskofficer,,,,']);
Route::get('returning_student', [ DeskController::class,'returning_student'])->middleware(['roles:Deskofficer,,,,']);
//Route::post('postReturningStudent', [ DeskController::class,'postReturningStudent'])->middleware(['roles:Deskofficer,,,,']);
Route::get('postReturningStudent', [ DeskController::class,'postReturningStudent'])->middleware(['roles:Deskofficer,,,,']);
Route::get('getReturningStudent', [ DeskController::class,'getReturningStudent'])->middleware(['roles:Deskofficer,,,,']);
Route::post('registerReturningStudent', [ DeskController::class,'registerReturningStudent'])->middleware(['roles:Deskofficer,,,,']);
//Route::get('registerReturningStudent', [ DeskController::class,'registerReturningStudent'])->middleware(['roles:Deskofficer,,,,']);
Route::get('view_student', [ DeskController::class,'view_student'])->middleware(['roles:Deskofficer,examsofficer,,,']);
Route::post('view_student', [ DeskController::class,'post_view_student'])->middleware(['roles:Deskofficer,examsofficer,,,']);
Route::get('register_student', [ DeskController::class,'register_student'])->middleware(['roles:Deskofficer,examsofficer,,,']);
Route::get('post_register_student/{fos_id?}/{level?}/{semester_id?}/{session?}/{season?}', [ DeskController::class,'post_register_student'])->middleware(['roles:Deskofficer,examsofficer,,,']);
Route::get('view_student_detail/{id}', [ DeskController::class,'view_student_detail'])->middleware(['roles:Deskofficer,,,,']);
Route::get('register_student_ii', [ DeskController::class,'register_student_ii'])->middleware(['roles:Deskofficer,examsofficer,,,']);
Route::get('post_register_student_ii/{fos_id?}/{level?}/{session?}/{season?}', [ DeskController::class,'post_register_student_ii'])->middleware(['roles:Deskofficer,examsofficer,,,']);
Route::get('registered_student_detail/{user_id?}/{level?}/{session?}/{season?}', [ DeskController::class,'registered_student_detail'])->middleware(['roles:Deskofficer,examsofficer,,,']);
Route::get('registered_student_detail_update/{user_id?}/{level?}/{session?}/{season?}', [ DeskController::class,'registered_student_detail_update'])->middleware(['roles:Deskofficer,examsofficer,,,']);
Route::get('registered_student_detail_delete/{user_id?}/{level?}/{session?}/{season?}', [ DeskController::class,'registered_student_detail_delete'])->middleware(['roles:Deskofficer,examsofficer,,,']);
Route::get('registered_student_detail_update_any/{user_id?}/{level?}/{session?}/{season?}', [ DeskController::class,'registered_student_detail_update_any'])->middleware(['roles:Deskofficer,examsofficer,,,']);

Route::post('update_entry_year', [ DeskController::class,'update_entry_year'])->middleware(['roles:Deskofficer,,,,']);
// registered result mode entering

//post result updated version

Route::post('postResult', [ DeskController::class,'postResult'])->middleware(['roles:Deskofficer,examsofficer,support,,']);


Route::post('entering_result', [ DeskController::class,'enter_result'])->middleware(['roles:Deskofficer,examsofficer,,,']);
Route::get('register_student/{fos_id?}/{l_id?}/{semester_id?}/{session?}/{season?}', [ DeskController::class,'get_register_student'])->middleware(['roles:Deskofficer,examsofficer,,,']);

Route::post('more_result', [ DeskController::class,'more_result'])->middleware(['roles:Deskofficer,,,,']);
Route::post('post_more_result', [ DeskController::class,'post_more_result'])->middleware(['roles:Deskofficer,,,,']);

// entering result per course
Route::get('e_result', [ DeskController::class,'e_result'])->middleware(['roles:Deskofficer,,,,']);
Route::post('e_result', [ DeskController::class,'e_result_next'])->middleware(['roles:Deskofficer,,,,']);
Route::get('e_result_c', [ DeskController::class,'e_result_c'])->middleware(['roles:Deskofficer,,,,']);
Route::post('insert_result', [ DeskController::class,'insert_result'])->middleware(['roles:Deskofficer,,,,']);
Route::get('insert_result/{y}', [ DeskController::class,'insert_result'])->middleware(['roles:Deskofficer,,,,']);
Route::post('excel_insert_result', [ DeskController::class,'excel_insert_result'])->middleware(['roles:Deskofficer,examsofficer,lecturer,HOD,,,']);
Route::get('resultUploadRight', [ DeskController::class,'resultUploadRight'])->middleware(['roles:Deskofficer,examsofficer,lecturer,HOD,,,']);
Route::post('uploadRight', [ DeskController::class,'uploadRight'])->middleware(['roles:Deskofficer,examsofficer,lecturer,HOD,,,']);
Route::post('excel_insert_result_gss', [ DeskController::class,'excel_insert_result_gss'])->middleware(['roles:Deskofficer,examsofficer,lecturer,HOD,,,']);
Route::post('excel_insert_result_gssII', [ DeskController::class,'excel_insert_result_gssII'])->middleware(['roles:Deskofficer,examsofficer,lecturer,HOD,,,']);


// entering probation result per course
Route::get('enter_probation_result', [ DeskController::class,'enter_probation_result'])->middleware(['roles:Deskofficer,examsofficer,,,']);
Route::get('enter_probation_result1', [ DeskController::class,'enter_probation_result_next'])->middleware(['roles:Deskofficer,examsofficer,,,']);
Route::post('probation_entering_result', [ DeskController::class,'probation_enter_result'])->middleware(['roles:Deskofficer,examsofficer,,,']);
Route::get('get_register_probation_student/{programme_id?}/{fos_id?}/{l_id?}/{session?}/{season?}', [ DeskController::class,'get_register_probation_student'])->middleware(['roles:Deskofficer,examsofficer,,,']);

//Route::post('insert_result', [ DeskController::class,'insert_result'])->middleware(['roles:Deskofficer,,,,']);

// view result
Route::post('view_result', [ DeskController::class,'post_view_result'])->middleware(['roles:Deskofficer,,,,']);
Route::post('view_result_detail', [ DeskController::class,'view_result_detail'])->middleware(['roles:Deskofficer,,,,']);
Route::get('view_result', [ DeskController::class,'view_result'])->middleware(['roles:Deskofficer,,,,']);


// delete result
Route::post('delete_result', [ DeskController::class,'post_delete_result'])->middleware(['roles:Deskofficer,,,,']);
Route::post('delete_result_detail', [ DeskController::class,'delete_result_detail'])->middleware(['roles:Deskofficer,,,,']);
Route::get('delete_result_detail', [ DeskController::class,'delete_result_detail'])->middleware(['roles:Deskofficer,,,,']);
Route::get('delete_result', [ DeskController::class,'delete_result'])->middleware(['roles:Deskofficer,,,,']);
Route::get('delete_desk_result/{id}', [ DeskController::class,'delete_desk_result'])->middleware(['roles:Deskofficer,,,,']);
Route::post('delete_desk_multiple_result', [ DeskController::class,'delete_desk_multiple_result'])->middleware(['roles:Deskofficer,,,,']);
// report  
Route::get('reports', [ DeskController::class,'report'])->middleware(['roles:Deskofficer,examsofficer,HOD,admin,support,,,']);
Route::get('getreport', [ DeskController::class,'post_report'])->middleware(['roles:Deskofficer,examsofficer,HOD,admin,support,,,']);
Route::get('reports1', [ SupportController::class,'reports1'])->middleware(['roles:DVC,,,,,']);
Route::get('getreport1', [DeskController::class,'post_report'])->middleware(['roles:DVC,,,,,']);
Route::get('departmentreport', [ DeskController::class,'departmentreport'])->middleware(['roles:examsofficer,HOD,,,']);

//approve result
Route::get('approveResult', [ SupportController::class,'approveResult'])->middleware(['roles:DVC,,,,,']);
Route::get('approveResult1', [DeskController::class,'post_report'])->middleware(['roles:DVC,,,,,']);
Route::post('approveResult', [ SupportController::class,'postApproveResult'])->middleware(['roles:DVC,,,,,']);


//setup graduate
Route::get('setupGraduate', [ SupportController::class,'setupGraduate'])->middleware(['roles:DVC,,,,,']);
Route::get('setupGraduate1', [DeskController::class,'post_report'])->middleware(['roles:DVC,,,,,']);
Route::post('setupGraduate', [ SupportController::class,'postSetupGraduate'])->middleware(['roles:DVC,,,,,']);

//view graduate
Route::get('viewGraduate', [ SupportController::class,'viewGraduate'])->middleware(['roles:DVC,,,,,']);
Route::get('oldportalviewGraduate', [ SupportController::class,'oldportalviewGraduate'])->middleware(['roles:DVC,,,,,']);
Route::get('firstClassStudent', [ SupportController::class,'firstClassStudent'])->middleware(['roles:DVC,,,,,']);
Route::post('firstClassStudent', [ SupportController::class,'postfirstClassStudent'])->middleware(['roles:DVC,,,,,']);
 /*
Route::get('ppp', [SupportController::class,'ppp'])->middleware(['roles:DVC,,,,,']);
*/
Route::post('viewGraduate', [ SupportController::class,'postViewGraduate'])->middleware(['roles:DVC,,,,,']);
Route::post('oldportalviewGraduate', [ SupportController::class,'postoldportalViewGraduate'])->middleware(['roles:DVC,,,,,']);
//===================== courses with no result =================================
Route::get('course_with_no_result', [ HomeController::class,'course_with_no_result'])->middleware(['roles:admin,support,DVC,,']);
Route::post('course_with_no_result', [ HomeController::class,'post_course_with_no_result'])->middleware(['roles:admin,support,DVC,,']);


//===================== courses with no result ii=================================
Route::get('courseWithNoResult', [ HomeController::class,'courseWithNoResult'])->middleware(['roles:admin,support,DVC,Deskofficer,']);
Route::post('courseWithNoResult', [ HomeController::class,'postCourseWithNoResult'])->middleware(['roles:admin,support,DVC,Deskofficer,']);

//===================== withdral old portal=================================
Route::get('withdrawalOldportal', [ HomeController::class,'withdrawalOldportal'])->middleware(['roles:admin,support,DVC,Deskofficer,']);
Route::post('withdrawalOldportal', [ HomeController::class,'postWithdrawalOldportal'])->middleware(['roles:admin,support,DVC,Deskofficer,']);


//===============================course and result ratio===================
Route::get('coursesAndResultRatio', [ HomeController::class,'coursesAndResultRatio']);
//Route::get('coursesAndResultRatio', [ HomeController::class,'coursesAndResultRatio'])->middleware(['roles:admin,support,DVC,Deskofficer,HOD,']);

Route::post('coursesAndResultRatio', [ HomeController::class,'postCoursesAndResultRatio']);
//================== Exams Officer ===============================================


Route::get('getfos/{id}',  [ExamofficerController::class,'getfos_hod'])->middleware(['roles:examsofficer,lecturer,HOD,,,']);

Route::get('eo_result_c_gssII', [ExamofficerController::class,'eo_result_c_gssII'])->middleware(['roles:examsofficer,lecturer,HOD,,,']);

Route::post('eo_assign_courses',  [ExamofficerController::class,'eo_assign_courses'])->middleware(['roles:examsofficer,lecturer,HOD,,,']);
Route::get('eo_result_c_gss', [ExamofficerController::class,'eo_result_c_gss'])->middleware(['roles:examsofficer,lecturer,HOD,,,']);
Route::get('eo_result_c', [ExamofficerController::class,'eo_result_c'])->middleware(['roles:examsofficer,lecturer,HOD,,,']);
Route::post('eo_insert_result', [ExamofficerController::class,'eo_insert_result'])->middleware(['roles:examsofficer,lecturer,HOD,,,']);
Route::post('v_result', [ExamofficerController::class,'post_v_result'])->middleware(['roles:examsofficer,lecturer,HOD,,,']);
Route::post('excel_eo_insert_result', [ExamofficerController::class,'excel_eo_insert_result'])->middleware(['roles:examsofficer,lecturer,HOD,,,']);

Route::get('v_result', [ExamofficerController::class,'v_result'])->middleware(['roles:examsofficer,lecturer,HOD,,,']);
Route::post('d_result', [ExamofficerController::class,'display_result'])->middleware(['roles:examsofficer,lecturer,HOD,,,']);

Route::get('v_result_gss', [ExamofficerController::class,'v_result_gss'])->middleware(['roles:examsofficer,lecturer,HOD,,,']);
Route::post('post_v_result_gss', [ExamofficerController::class,'post_v_result_gss'])->middleware(['roles:examsofficer,lecturer,HOD,,,']);
Route::post('d_result_gss', [ExamofficerController::class,'display_result_gss'])->middleware(['roles:examsofficer,lecturer,HOD,,,']);

Route::get('lecturer',  [ExamofficerController::class,'index'])->middleware(['roles:examsofficer,lecturer,HOD,,,']);
Route::get('lecturer_gss',  [ExamofficerController::class,'lecturer_gss'])->middleware(['roles:examsofficer,lecturer,HOD,,,']);
Route::post('lecturer_gss',  [ExamofficerController::class,'post_lecturer_gss'])->middleware(['roles:examsofficer,lecturer,HOD,,,']);
// delete result

Route::get('eo_delete_result',  [ExamofficerController::class,'eo_delete_result'])->middleware(['roles:examsofficer,lecturer,HOD,,,']);
Route::post('eo_delete_result',  [ExamofficerController::class,'post_eo_delete_result'])->middleware(['roles:examsofficer,lecturer,HOD,,,']);
Route::post('eo_delete_result_detail', [ExamofficerController::class,'eo_delete_result_detail'])->middleware(['roles:examsofficer,lecturer,HOD,,,']);
Route::get('eo_delete_desk_result/{id}', [ExamofficerController::class,'eo_delete_desk_result'])->middleware(['roles:examsofficer,lecturer,HOD,,,']);
Route::post('eo_delete_desk_multiple_result', [ExamofficerController::class,'eo_delete_desk_multiple_result'])->middleware(['roles:examsofficer,lecturer,HOD,,,']);
Route::get('eo_delete_result_detail', [ExamofficerController::class,'eo_delete_result_detail'])->middleware(['roles:examsofficer,lecturer,HOD,,,']);


// register student
Route::get('r_student', [ExamofficerController::class,'r_student'])->middleware(['roles:examsofficer,lecturer,,,']);
Route::post('r_student', [ExamofficerController::class,'post_r_student'])->middleware(['roles:examsofficer,lecturer,,,']);
Route::post('d_student', [ExamofficerController::class,'d_student'])->middleware(['roles:examsofficer,lecturer,HOD,,,']);

//========================== public result =======================================
Route::get('publish_result', [ HomeController::class,'publish_result'])->middleware(['roles:admin,support,Deskofficer,,,']);
Route::get('get_publish_result', [ HomeController::class,'post_publish_result'])->middleware(['roles:admin,support,Deskofficer,,,']);
Route::post('publish', [ HomeController::class,'publish'])->middleware(['roles:admin,support,Deskofficer,,,']);

//=============================== update officer email ====================================
Route::get('update_officer_email', [GeneralController::class,'update_officer_email'])->middleware(['roles:admin,support,,,']);
//======================== student management=====================================
Route::get('studentManagement', [ DeskController::class,'studentManagement'])->middleware(['roles:Deskofficer,admin,support,examsofficer,HOD,']);
Route::get('studentManagementAddCourses', [ DeskController::class,'studentManagementAddCourses'])->middleware(['roles:Deskofficer,admin,support,examsofficer,HOD,']);
Route::get('getStudentManagementAddCourse', [ DeskController::class,'getStudentManagementAddCourse'])->middleware(['roles:Deskofficer,admin,support,examsofficer,HOD,']);
Route::post('postStudentManagementAddCourse', [ DeskController::class,'postStudentManagementAddCourse'])->middleware(['roles:Deskofficer,admin,support,examsofficer,HOD,']);
// repeat
Route::get('studentManagementAddRepeatCourses', [ DeskController::class,'studentManagementAddRepeatCourses'])->middleware(['roles:Deskofficer,admin,support,examsofficer,HOD,']);
Route::get('getStudentManagementAddRepeatCourse', [ DeskController::class,'getStudentManagementAddRepeatCourse'])->middleware(['roles:Deskofficer,admin,support,examsofficer,HOD,']);
Route::post('postStudentManagementAddRepeatCourse', [ DeskController::class,'postStudentManagementAddRepeatCourse'])->middleware(['roles:Deskofficer,admin,support,examsofficer,HOD,']);

// carryover
Route::get('studentManagementAddCarryOverCourses', [ DeskController::class,'studentManagementAddCarryOverCourses'])->middleware(['roles:Deskofficer,admin,support,examsofficer,HOD,']);
Route::get('getStudentManagementAddCarryOverCourse', [ DeskController::class,'getStudentManagementAddCarryOverCourse'])->middleware(['roles:Deskofficer,admin,support,examsofficer,HOD,']);
Route::post('postStudentManagementAddCarryOverCourse', [ DeskController::class,'postStudentManagementAddCarryOverCourse'])->middleware(['roles:Deskofficer,admin,support,examsofficer,HOD,']);

// repeat 2
Route::get('studentManagementAddRepeatCourses2', [ DeskController::class,'studentManagementAddRepeatCourses2'])->middleware(['roles:Deskofficer,admin,support,examsofficer,HOD,']);
Route::get('getStudentManagementAddRepeatCourse2', [ DeskController::class,'getStudentManagementAddRepeatCourse2'])->middleware(['roles:Deskofficer,admin,support,examsofficer,HOD,']);
Route::post('postStudentManagementAddRepeatCourse2', [ DeskController::class,'postStudentManagementAddRepeatCourse2'])->middleware(['roles:Deskofficer,admin,support,examsofficer,HOD,']);

// carryover2
Route::get('studentManagementAddCarryOverCourses2', [ DeskController::class,'studentManagementAddCarryOverCourses2'])->middleware(['roles:Deskofficer,admin,support,examsofficer,HOD,']);
Route::get('getStudentManagementAddCarryOverCourse2', [ DeskController::class,'getStudentManagementAddCarryOverCourse2'])->middleware(['roles:Deskofficer,admin,support,examsofficer,HOD,']);
Route::post('postStudentManagementAddCarryOverCourse2', [ DeskController::class,'postStudentManagementAddCarryOverCourse2'])->middleware(['roles:Deskofficer,admin,support,examsofficer,HOD,']);

//======================= transfer student==============================
Route::post('transferStudents', [ HomeController::class,'transferStudents'])->middleware(['roles:admin,support,Deskofficer,,']);

//Auth::routes();
Route::get('logout',[AuthenticatedSessionController::class,'destroy']);

//=========================== audit ==================================
Route::get('auditCourseCode', [AuditController::class,'courseCode'])->middleware(['roles:DVC,admin,support,,,']);
Route::get('getAuditCourseCode', [AuditController::class,'getCourseCode'])->middleware(['roles:DVC,admin,support,,,']);
Route::get('auditCourseCodeOld', [AuditController::class,'courseCodeOld'])->middleware(['roles:DVC,admin,support,,,']);
Route::get('getAuditCourseCodeOld', [AuditController::class,'getCourseCodeOld'])->middleware(['roles:DVC,admin,support,,,']);

//================================= correction name=============================================
Route::get('correctionName/{id}',  [GeneralController::class,'correctionName'])->middleware(['roles:Deskofficer,,,,,']);

Route::post('correctionName',  [GeneralController::class,'updateCorrectionName'])->middleware(['roles:Deskofficer,,,,,']);

//====================================student information ============================================
//get registered Students
Route::get('studentsInformation', [GeneralController::class,'studentsInformation']);
Route::post('studentsInformation', [GeneralController::class,'postStudentsInformation']);
//=================================moppedUp exams================================

Route::get('moppedUpRegisteredStudents', [MoppedUpController::class,'index']);
Route::post('moppedUpRegisteredStudents', [MoppedUpController::class,'postMoppedUp']);
Route::get('downloadMopUpERS/{id}/{d}', [MoppedUpController::class,'mopUpERS']);
Route::get('downloadMopUpERS/{id}', [MoppedUpController::class,'mopUpERS2']);
Route::get('downloadMopUpERS3', [MoppedUpController::class,'mopUpERS3']);
Route::get('uploadMopUpResult/{id}/{d}', [MoppedUpController::class,'uploadMopUpResult'])->middleware(['roles:examsofficer,lecturer,HOD,Deskofficer,,']);
Route::post('uploadMopUpResult', [MoppedUpController::class,'excelInsertMopUPResult'])->middleware(['roles:examsofficer,lecturer,HOD,Deskofficer,,']);
Route::post('assignCoursesMopUp', [MoppedUpController::class,'assignCoursesMopUp'])->middleware(['roles:HOD,Deskofficer,,,,']);
Route::get('viewMopUpResult/{id}/{d}', [MoppedUpController::class,'viewMopUpResult'])->middleware(['roles:examsofficer,lecturer,HOD,DVC,support']);
Route::get('remove_mop_assign_course/{id}', [MoppedUpController::class,'removeMopAssignCourse'])->middleware(['roles:,,HOD,,,']);

//=================================enable department for result================
Route::get('enableResultDepartment', [GeneralController::class,'enableResultDepartment'])->middleware(['roles:DVC,,,,']);
Route::post('enableResultDepartment', [GeneralController::class,'postEnableResultDepartment'])->middleware(['roles:DVC,,,,']);
Route::post('updateEnableResultDepartment', [GeneralController::class,'updateEnableResultDepartment'])->middleware(['roles:DVC,,,,']);
Route::get('viewEnableResultDepartment', [GeneralController::class,'viewEnableResultDepartment'])->middleware(['roles:DVC,,,,']);
Route::post('viewEnableResultDepartment', [GeneralController::class,'reverseEnableResultDepartment'])->middleware(['roles:DVC,,,,']);


//=================================enable department for result old================
Route::get('enableResultDepartmentOld', [GeneralController::class,'enableResultDepartmentOld'])->middleware(['roles:DVC,,,,']);
Route::post('enableResultDepartmentOld', [GeneralController::class,'postEnableResultDepartmentOld'])->middleware(['roles:DVC,,,,']);
Route::post('updateEnableResultDepartmentOld', [GeneralController::class,'updateEnableResultDepartmentOld'])->middleware(['roles:DVC,,,,']);
Route::get('viewEnableResultDepartmentOld', [GeneralController::class,'viewEnableResultDepartmentOld'])->middleware(['roles:DVC,,,,']);
Route::post('viewEnableResultDepartmentOld', [GeneralController::class,'reverseEnableResultDepartmentOld'])->middleware(['roles:DVC,,,,']);
Route::get('re', [HomeController::class,'re']);
//=========================================student controller======================
Route::get('studentsWithOnlyFirstSemester', [StudentController::class,'studentsWithOnlyFirstSemester'])->middleware(['roles:admin,support,Deskofficer,,']);
Route::post('studentsWithOnlyFirstSemester', [StudentController::class,'postStudentsWithOnlyFirstSemester'])->middleware(['roles:admin,support,Deskofficer,,']);

Route::get('changeJambRegToPassword/{entry_year}', [GeneralController::class,'changeJambRegToPassword']);
Route::get('upd', [MoppedUpController::class,'upd']);

//================================ log=================================
Route::get('mopUpWithCALog', [MoppedUpController::class,'mopUpWithCALog']);
Route::get('upRc/{s}/{fos}', [MoppedUpController::class,'upRc']);
Route::get('usr', [SupportController::class,'usr']);
Route::get('usrcr', [SupportController::class,'usrcr']);
Route::get('probationSt', [SupportController::class,'probationSt']);
//===========================duplicate name========================
Route::get('duplicateName', [SupportController::class,'duplicateName']);