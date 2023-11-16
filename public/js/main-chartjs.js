// --------------
// CONFIG CHART
// --------------

// CHART USER BARU
const setNewUsersChart = (title, labels, datas) => {
	const labelsUserBaru = labels;
	const dataUserBaru = {
		labels: labelsUserBaru,
		datasets: [
			{
				label: title,
				data: datas,
				backgroundColor: ["rgb(18,105,219, 0.2)"],
				borderColor: ["rgb(18,105,219)"],
				borderWidth: 1,
			},
		],
	};

	const configUserBaru = {
		type: "bar",
		data: dataUserBaru,
		options: {
			scales: {
				y: {
					beginAtZero: true,
				},
			},
		},
	};

	const ChartUserBaru = new Chart(
		document.getElementById("ChartUserBaru"),
		configUserBaru
	);
}

// CHART Pembelian By Omset Perbulan
const setOmsetMonthlyChart = (title, labels, datas) => {
	const labelsPembelianByOmsetPerbulan = labels;
	const dataPembelianByOmsetPerbulan = {
		labels: labelsPembelianByOmsetPerbulan,
		datasets: [
			{
				label: title,
				backgroundColor: "rgb(18,105,219)",
				borderColor: "rgb(18,105,219)",
				data: datas,
			},
		],
	};

	const configPembelianByOmsetPerbulan = {
		type: "line",
		data: dataPembelianByOmsetPerbulan,
		options: {
			scales: {
				y: {
					beginAtZero: true,
				},
			},
		},
	};

	const ChartPembelianByOmsetPerbulan = new Chart(
		document.getElementById("ChartPembelianByOmsetPerbulan"),
		configPembelianByOmsetPerbulan
	);
}



// CHART Upload Visibility
const setUploadVisibilityChart = (title, labels, datas) => {
	const labelsUploadVisibility = labels;
	const dataUploadVisibility = {
		labels: labelsUploadVisibility,
		datasets: [
			{
				label: title,
				backgroundColor: "rgb(18,105,219)",
				borderColor: "rgb(18,105,219)",
				data: datas,
			},
		],
	};

	const configUploadVisibility = {
		type: "line",
		data: dataUploadVisibility,
		options: {
			scales: {
				y: {
					beginAtZero: true,
				},
			},
		},
	};

	const ChartUploadVisibility = new Chart(
		document.getElementById("ChartUploadVisibility"),
		configUploadVisibility
	);
}

// CHART Total Penukaran Point
const setRewardRedeemPointsChart = (title, labels, datas) => {
	const labelsTotalPenukaranPoint = labels;
	const dataTotalPenukaranPoint = {
		labels: labelsTotalPenukaranPoint,
		datasets: [
			{
				label: title,
				data: datas,
				backgroundColor: ["rgb(18,105,219, 0.2)"],
				borderColor: ["rgb(18,105,219)"],
				borderWidth: 1,
			},
		],
	};

	const configTotalPenukaranPoint = {
		type: "bar",
		data: dataTotalPenukaranPoint,
		options: {
			scales: {
				y: {
					beginAtZero: true,
				},
			},
		},
	};

	const ChartTotalPenukaranPoint = new Chart(
		document.getElementById("ChartTotalPenukaranPoint"),
		configTotalPenukaranPoint
	);
}
