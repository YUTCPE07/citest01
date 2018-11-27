<style>
	.profile-img {
		width: 150px;
		position:relative;
	}

	.profile-boxHead {
		position: relative;
	}

	.profile-btn-edit {
		position: absolute;
		right: 50px;
	}

	.profile-imgAddPictureBox {
		position:absolute;
		right: 0px;
		top: 120px;
		color: white;
		width: 50px;

	}

	.profile-imgAddPicture {
		color: red;
		background-color: green;
	}

	.hrRow {
		border-color: var(--color-green);
	}

	.borderYGreen {
		border-top: 1px solid var(--color-green);
		border-bottom: 1px solid var(--color-green);
	}

	.catarogyBox {
		border: 1px solid var(--color-gray2);
		border-radius: 0.75rem;
	}

	.catarogyBox-active {
		border: 1px solid var(--color-green);
		background-color: var(--color-green);
		color:white;
	}


</style>


<div class="h4" >

	<div class="container shadow mt-5 pt-5 borderYGreen">
		<div class="d-flex justify-content-center profile-boxHead">
			<div class="profile-img">
				<img src="https://via.placeholder.com/400x400" class="rounded rounded-circle img-responsive home_brand shadow" alt=" ">
				<div class="profile-imgAddPictureBox">
					<img src="<?php echo base_url(); ?>assets/images/user/profileAdd.png">
				</div>
			</div>
			<div class="">
				<div class="h1 meduim pt-5 px-5 text-green">แครอท หัวเขียว</div>
			</div>
			<button class="btn btn-primary profile-btn-edit px-5">แก้ไข</button>
		</div> <!-- end d-flex justify-content-center -->

		<div class="container p-lg-5">
			<div class="row">
				<div class="col-lg-12 text-green">ข้อมูลส่วนตัว</div>
			</div>
			<hr class="hrRow">

			<div class="row">
				<div class="col-lg-2">
					<div class="text-gray2">คำนำหน้าชื่อ</div>
					<div class="medium">นางสาว</div>
				</div>
				<div class="col-lg-4">
					<div class="text-gray2">ชื่อ</div>
					<div class="medium">แครอท</div>
				</div>
				<div class="col-lg-4">
					<div class="text-gray2">นามสกุล</div>
					<div class="medium">แก้วหัวเขียว</div>
				</div>
				<div class="col-lg-2">
					<div class="text-gray2">ชื่อเล่น</div>
					<div class="medium">ส้ม</div>
				</div>
			</div>

			<div class="row pt-lg-5">
				<div class="col-lg-2">
					<div class="text-gray2">เพศ</div>
					<div class="medium">ไม่ระบุ</div>
				</div>
				<div class="col-lg-4">
					<div class="text-gray2">สถานะ</div>
					<div class="medium">โสด</div>
				</div>
				<div class="col-lg-4">
					<div class="text-gray2">จำนวนบุตร</div>
					<div class="medium">-</div>
				</div>
				<div class="col-lg-2">
					<div class="text-gray2">สัญชาติ</div>
					<div class="medium">ไทย</div>
				</div>
			</div>

			<div class="row pt-lg-5">
				<div class="col-lg-2">
					<div class="text-gray2">การศึกษา</div>
					<div class="medium">ปริญญาตรี</div>
				</div>
				<div class="col-lg-4">
					<div class="text-gray2">เลขที่บัตรประชาชน</div>
					<div class="medium">1 - 2323 - 23334 - 23 - 3</div>
				</div>
				<div class="col-lg-4">
					<div class="text-gray2">เลขที่หนังสือเินทาง</div>
					<div class="medium">AA - 1234567</div>
				</div>
			</div>

			<div class="row pt-lg-5">
				<div class="col-lg-2">
					<div class="text-gray2">วันเกิด</div>
					<div class="medium">10/10/2536</div>
				</div>
				<div class="col-lg-10">
					<br>
					<div class="medium text-green cursor-pointer"><u>แก้ไขวันเกิด</u></div>
				</div>
			</div>

		</div>

		<div class="container p-lg-5">
			<div class="row">
				<div class="col-lg-12 text-green">ความสนใจ</div>
			</div>
			<hr class="hrRow">

			<div class="row ">
				<div class="col-lg-3">
					<div class="text-center p-2 catarogyBox">กิจกรรมกลางเเจ้ง</div>
				</div>
				<div class="col-lg-3">
					<div class="text-center p-2 catarogyBox catarogyBox-active">asdsadas</div>
				</div>
			</div>
		</div>

		<div class="container p-lg-5">
			<div class="row">
				<div class="col-lg-12 text-green">ข้อมูลติดต่อ</div>
			</div>
			<hr class="hrRow">

			<div class="row">
				<div class="col-lg-12">
					<div class="text-gray2">ที่อยู่</div>
					<div class="medium">ฟหกฟหกหฟกฟหกฟห หกฟกหฟก ฟหกฟหก ฟหก ฟห กฟหก ฟห ฟห ฟหกฟห ฟห ฟหกๅ 12223</div>
				</div>
				<div class="col-lg-4">
					<div class="text-gray2">เบอร์โทรบ้าน</div>
					<div class="medium">06-2392323</div>
				</div>
				<div class="col-lg-4">
					<div class="text-gray2">เบอร์โทรที่ทำงาน</div>
					<div class="medium">-</div>
				</div>
				<div class="col-lg-4">
					<div class="text-gray2">อีเมล</div>
					<div class="medium">wqwww@sfds.con</div>
				</div>
				<div class="col-lg-12">
					<div class="text-gray2">ID Line</div>
					<div class="medium">sasss</div>
				</div>
				<div class="col-lg-12">
					<div class="text-gray2">สถานที่ทำงาน</div>
					<div class="medium">หฟกฟหกหฟกฟหกห หฟกฟหกฟหกหก ฟหกหฟกหฟกหฟก ฟหก ฟกฟหกห ฟกฟห กหฟกฟห ฟห</div>
				</div>
			</div>
		</div>

	</div> <!-- end container -->

	<div class="container p-lg-5 shadow my-5 py-5 borderYGreen">
		<div class="container">
			<div class="row">
				<div class="col-lg-12">
					<div class="text-gray2">เบอร์โทรศัพท์(มือถือ)</div>
					<div class="medium">06-2392323</div>
				</div>
			</div>
		</div>
	</div> <!-- end container -->


	<div class="container p-lg-5 shadow my-5 py-5 borderYGreen">
		<div class="container">
			<div class="row">
				<div class="col-lg-4">
					<div class="medium mb-2">เบอร์โทรศัพท์(มือถือ)</div>
					<input class="bg-gray3 text-gray1" type="tel" name="otp_phone" disabled value="075-2321212">
				</div>
				<div class="col-lg-8">
					<br>
					<button class="btn btn-lg btn-primary bold">รับ OTP</button>
					<small class="px-3 text-gray1">*รหัส OTP จะถูกส่งไปที่เบอร์เดิม</small>
				</div>
			</div>
			<div class="row my-4">
				<div class="col-lg-4">
					<div class="medium mb-2">รหัส OTP : 1</div>
					<input class="bg-gray3 text-gray1" type="tel" name="otp_phone" placeholder="รหัส OTP เบอร์เดิม">
				</div>
				<div class="col-lg-8">
					<br>
					<button class="btn btn-lg btn-primary bold">รับ OTP</button>
					<small class="px-3 text-gray1">*รหัส OTP จะถูกส่งไปที่เบอร์ใหม่</small>
				</div>
			</div>
			<div class="row">
				<div class="col-lg-4">
					<div class="medium mb-2">รหัส OTP : 2</div>
					<input class="bg-gray3 text-gray1" type="tel" name="otp_phone" placeholder="รหัส OTP เบอร์ใหม่" >
				</div>
				<div class="col-lg-8">
					<br>
					<button class="btn btn-lg btn-primary bold px-4 active">ยืนยัน</button>
				</div>
			</div>
		</div>
	</div> <!-- end container -->
</div>



<div class="hr_footer_height"></div>
