/**
*       Helper Function JS
*
*/


/**
*       Menyelipkan csrf token pada setup ajax
*
*/
const ajaxSetup = () => {
	$.ajaxSetup({
		'headers' : {
			'X-CSRF-TOKEN' : $('meta[name="_token"]').attr('content'),
		}
	});
}



/**
*       Config Toastr
*
*/
const toastrAlert = () => {
	toastr.options = {
		closeButton: true,
		progressBar: true,
		showMethod: 'slideDown',
		timeOut: 4000
	};
}



/**
*       Clear invalid class pada form
*
*/
const clearInvalid = () => {
	$('.is-invalid').removeClass('is-invalid');
	$('.has-invalid').removeClass('has-invalid');
	$('.invalid-feedback').html('');
}



/**
*       Format number
*       @param Int num
*
*/
const numberFormat = num => {
	if($.isNumeric(num)) {
		return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g,".");
	} else {
		return num;
	}
}



/**
*       Pengecekan variable kosong atau tidak
*       @param data
*
*/
const isEmpty = data => {
	if(data == null || data == "" || data == undefined) {
		return true;
	} else {
		return false;
	}
}

/**
*       Formatting date
*       @require moment js
*       @param String date
*       @param String format
*       @param String toFormat
*
*/
const formatDate = (date, format, toFormat) => {
	return moment(date, format).format(toFormat);
}



/**
*       Huruf depan kapital
*       @param String text
*
*/
const ucfirst = text => {
	return text.charAt(0).toUpperCase() + text.slice(1);
}



/**
*       Tombol process
*       @param jQueryHtmlDomElement element
*       @param String html (optional)
*
*/
const processingButton = (element, html = null) => {
	element.attr('disabled', '');
	if(isEmpty(html)) {
		element.html(`<i class="mdi mdi-loading mdi-spin"></i> Memproses..`);
	} else {
		element.html(html);
	}
}


/**
*       Tombol process selesai
*       @param jQueryHtmlDomElement element
*       @param String html (optional)
*
*/
const processingButtonDone = (element, html = null) => {
	element.removeAttr('disabled');
	if(isEmpty(html)) {
		element.html(`<i class="mdi mdi-check"></i> Simpan`);
	} else {
		element.html(html);
	}
}


/**
*       Tombol process berlanjut dengan mengganti content html dari button
*       @param jQueryHtmlDomElement element
*       @param String html (optional)
*
*/
const processingButtonContinue = (element, html = null) => {
	if(isEmpty(html)) {
		element.html(`<i class="mdi mdi-spin mdi-loading"></i> Sedang mengalihkan..`);
	} else {
		element.html(html);
	}
}



/**
*       Menampilkan invalid response
*       @param jQueryHtmlDomElement elem
*       @param Array response
*
*/
const invalidResponse = (elem, response) => {
	$.each(response, (i, d) =>{
		elem.find(`[name="${i}"]`).addClass('is-invalid');
		elem.find(`[name="${i}"]`).parents('.form-group').find('.invalid-feedback').html(d);
		elem.find(`[name="${i}"]`).parents('.form-group').find('.invalid-feedback').show();
		// $(`[name="${i}"]`).siblings('.invalid-feedback').show();
	})
}



/**
*       Menghapus class warna
*       @param jQueryHtmlDomElement elem
*       @param String except
*
*/
const clearColorText = (elem, except = null) => {
	let classList = ['text-danger', 'text-success'];
	$.each(classList, (i, theClass) => {
		if(except != null && theClass != `text-${except}`) {
			elem.removeClass(theClass);
		} else if (except == null) {
			elem.removeClass(theClass);
		}
	})
}



/**
*       Mengenable tombol
*       @param jQueryHtmlDomElement elem
*
*/
const enable = elem => {
	elem.removeAttr('disabled');
}



/**
*       Mendisable tombol
*       @param jQueryHtmlDomElement elem
*
*/
const disable = elem => {
	elem.attr('disabled', '');
}



/**
*       Download file dari data base64
*       @param String filedata  => base64 data
*       @param String mime      => mime type
*       @param String filename (opsional)   => Nama file
*
*/
const downloadFromBase64 = (filedata, mime, filename = null) => {
	let a = document.createElement("a");
	document.body.appendChild(a);
	a.href = `data:${mime};base64,${filedata}`;
	a.style = "display: none";

	if(!isEmpty(filename)) {
		a.download = filename;
	}

	a.click();
	a.remove();
}



/**
*       Download file dari data base64
*       @param String filedata  => base64 data
*       @param String mime      => mime type
*       @param String filename (opsional)   => Nama file
*
*/
const copyText = text => {
	let input = document.createElement("input");
	document.body.appendChild(input);
	input.value = text
	input.type = 'text'

	input.select();
	input.setSelectionRange(0, 99999); /* For mobile devices */

	document.execCommand('copy');
	input.remove();
}



/**

*/
const or = (value1 , value2) => {
	if(!isEmpty(value1)) return value1;

	return value2;
}


const setInvalidFeedback = (inputElement, message) => {
	$(inputElement).addClass('is-invalid');
	$(inputElement).parents('.form-group').find('.invalid-feedback').html(message);
}


const showWithSlide = (elem) => {
	$(elem).slideDown('slow');
}


const hideWithSlide = (elem) => {
	$(elem).slideUp('slow');
}


const showOrHideWithSlide = elem => {
	let jElem = $(elem);

	if(jElem.first().is(":hidden")) {
		showWithSlide(elem)
	} else {
		hideWithSlide(elem)
	}
}


const renderLibEvent = () => {

	$('.show-btn').off('click')
	$('.show-btn').on('click', function(){
		let target = $(this).data('target');

		showOrHideWithSlide(target);
	});
}


const notification = (title, message, type, icon) => {
	$.notify({
		'icon': icon,
		'title': title,
		'message': message,
	},{
		'type': type,
		'placement': {
		'from': "top",
		'align': "right"
		},
		'time': 1000,
	});
}


const infoNotification = (title, message) => {
	notification(title, message, 'info', 'flaticon-alarm-1');
}

const successNotification = (title, message) => {
	notification(title, message, 'success', 'flaticon-success');
}

const warningNotification = (title, message) => {
	notification(title, message, 'warning', 'flaticon-exclamation');
}

const errorNotification = (title, message) => {
	notification(title, message, 'danger', 'flaticon-error');
}

const ajaxSuccessHandling = (response) => {
	let { message } = response;
	successNotification('Berhasil', message)
}

const ajaxErrorHandling = (error, $form = null) => {
	let { status, responseJSON } = error;
	let { message } = responseJSON;

	if(status == 422) {
		if($form) {
			let { errors } = responseJSON;
			invalidResponse($form, errors);
		}

		if(message == "The given data was invalid.") {
			message = "Harap cek kembali form isian"
		}
	}

	message = message == "" ? 'XHR Invalid' : message;

	warningNotification('Peringatan', message);
}

const confirmation = (message, yesAction = null, cancelAction = null) => {
	$.confirm({
		title: 'Konfirmasi',
		content: message,
		buttons: {
			ya: {
				text: 'Ya',
				btnClass: 'btn-primary',
				keys: ['enter' ],
				action: function(){
					if(yesAction) {
						yesAction()
					}
				}
			},
			batal: {
				text: 'Batal',
				btnClass: 'btn-danger',
				keys: ['esc'],
				action: function(){
					if(cancelAction) {
						cancelAction()
					}
				}
			}
		}
	});
}


const validateBeforeMove = (tabButtonSelector, formSelector = '#form') => {
	if($(formSelector)[0].checkValidity()) {
		$(tabButtonSelector).click();
	} else {
		$(formSelector)[0].reportValidity();
	}
}


// reload window (5000)
function windowReload(delayTime) {
    setTimeout(function(){
        window.location.reload(true);
    },delayTime);
}

// redirect to url (5000, route('home'))
function redirectUrlTo(delayTime, url) {
    setTimeout(function(){
        window.location.href = url;
    },delayTime);
}

// window close automatic
function windowClose(delayTime){
    setTimeout(function() {
        window.close();
      }, delayTime);
}
