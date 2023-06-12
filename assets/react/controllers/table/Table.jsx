import React, { useState, useEffect } from 'react';
import DataTable from 'react-data-table-component';
import TableColumnsModel from './TableColumnsModel';

const DatasToDisplay = (prop) => {
	const [error, setError] = useState(null);
	const [datas, setDatas] = useState([]);
	const [dataTableTitle, setDataTableTitle] = useState('Posts List');
	const [isError, setIsError] = useState(false);
	const [columns, setColumns] = useState([]);

	useEffect(() => {
		TableColumnsModel(prop);
		setDataTableTitle(TableColumnsModel(prop).TableTitle);
		setDatas(TableColumnsModel(prop).Datas);
		setIsError(TableColumnsModel(prop).IsError);
		setColumns(TableColumnsModel(prop).Columns);
		setError(TableColumnsModel(prop).Error);
	}, [prop]);

	return (
		<div className='postsList__container'>
			{/* We check if an error occurred, if so, we display it, if not, we display the Table */}
			<div className='postsList__tableContainer'>
				{isError ? (
					<div className='error__warning__message'>{error}</div>
				) : (
					<DataTable
						title={dataTableTitle}
						columns={columns}
						data={datas}
						pagination
						paginationRowsPerPageOptions={[5, 10, 15, 20, 25, 30]}
						paginationPerPage={5}
						paginationComponentOptions={{
							rowsPerPageText: 'Rows per page:',
							rangeSeparatorText: 'of',
							noRowsPerPage: false,
							selectAllRowsItem: true,
							selectAllRowsItemText: 'All',
						}}
					/>
				)}
			</div>
		</div>
	);
};

export default DatasToDisplay;
