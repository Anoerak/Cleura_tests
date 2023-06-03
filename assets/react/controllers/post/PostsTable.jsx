import React, { useState, useEffect } from 'react';
import DataTable from 'react-data-table-component';

const PostsList = (prop) => {
	console.log(prop.posts[0].created_at.date.toString(''));
	const [error, setError] = useState(null);
	const [datas, setDatas] = useState([]);
	const [dataTableTitle, setDataTableTitle] = useState('Posts List');
	const [isError, setIsError] = useState(false);

	useEffect(() => {
		setDataTableTitle('Posts List');
		// We check if the Posts is empty
		if (prop.posts && prop.posts.length < 2) {
			// If so, we display an error message
			setIsError(true);
			setError('No post here!! Please create one.');
		} else {
			// Else, we set the datas to the state
			setDatas(prop.posts);
		}
	}, [prop]);

	// Columns for the DataTable component
	const columns =
		prop.posts && prop.posts.length > 0 && prop.posts[0].edit
			? [
					{
						name: 'ID',
						selector: (row) => row.id,
						sortable: true,
					},
					{
						name: 'Title',
						selector: (row) => row.title,
						sortable: true,
					},
					{
						name: 'Message',
						selector: (row) => row.content,
						sortable: true,
					},
					{
						name: 'Created At',
						// We convert the date to a string and split it to get only the date
						selector: (row) => row.created_at.date.toString('').split(' ')[0],
						sortable: true,
					},
					{
						name: 'Read',
						cell: (row) => (
							<div>
								<a href={`/post/${row.id}`}>Read</a>
							</div>
						),
						sortable: false,
					},
					{
						name: 'Modify',
						cell: (row) => (
							<div>
								<a href={`/post/${row.id}/edit`}>Edit</a>
							</div>
						),
						sortable: false,
					},
					{
						name: 'Delete',
						cell: (row) => (
							<div>
								<a href={`/post/${row.id}/delete`}>Delete</a>
							</div>
						),
						sortable: false,
					},
			  ]
			: [
					{
						name: 'ID',
						selector: (row) => row.id,
						sortable: true,
					},
					{
						name: 'Title',
						selector: (row) => row.title,
						sortable: true,
					},
					{
						name: 'Message',
						selector: (row) => row.content,
						sortable: true,
					},
					{
						name: 'Created At',
						// We convert the date to a string and split it to get only the date
						selector: (row) => row.created_at.date.toString('').split(' ')[0],
						sortable: true,
					},
					{
						name: 'Read',
						cell: (row) => (
							<div>
								<a href={`/post/${row.id}`}>Read</a>
							</div>
						),
						sortable: false,
					},
			  ];

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

export default PostsList;
