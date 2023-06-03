import React, { useState, useEffect } from 'react';
import DataTable from 'react-data-table-component';

const PostsList = (prop) => {
	console.log(prop.type);
	const [error, setError] = useState(null);
	const [datas, setDatas] = useState([]);
	const [dataTableTitle, setDataTableTitle] = useState('Posts List');
	const [isError, setIsError] = useState(false);
	const [columns, setColumns] = useState([]);

	useEffect(() => {
		// Columns for the DataTable component
		if (prop.users && prop.users.length > 0) {
			setDataTableTitle('Users List');
			setDatas(prop.users);
			setIsError(false);
			setColumns([
				{
					name: 'ID',
					selector: (row) => row.id,
					sortable: true,
				},
				{
					name: 'Name',
					selector: (row) => row.name,
					sortable: true,
				},
				{
					name: 'Email',
					selector: (row) => row.email,
					sortable: true,
				},
				{
					name: 'Role',
					selector: (row) => row.roles,
					sortable: true,
				},
				{
					name: 'Edit',
					cell: (row) => (
						<div>
							<a className='edit__button' href={`/user/${row.id}/edit`}>
								Edit
							</a>
						</div>
					),
					sortable: false,
				},
				{
					name: 'Delete',
					cell: (row) => (
						<div>
							<a className='delete__button' href={`/user/${row.id}/delete`}>
								Delete
							</a>
						</div>
					),
					sortable: false,
				},
			]);
		} else if (prop.forums && prop.forums.length > 0) {
			setDataTableTitle('Forums List');
			setDatas(prop.forums);
			setIsError(false);
			setColumns([
				{
					name: 'ID',
					selector: (row) => row.id,
					sortable: true,
				},
				{
					name: 'Name',
					selector: (row) => row.name,
					sortable: true,
				},
				{
					name: 'Edit',
					cell: (row) => (
						<div>
							<a className='edit__button' href={`/forum/${row.id}/edit`}>
								Edit
							</a>
						</div>
					),
					sortable: false,
				},
				{
					name: 'Delete',
					cell: (row) => (
						<div>
							<a className='delete__button' href={`/forum/${row.id}/delete`}>
								Delete
							</a>
						</div>
					),
					sortable: false,
				},
			]);
		} else if (prop.posts && prop.posts.length > 0 && prop.posts[0].edit) {
			setDataTableTitle('Posts List');
			setDatas(prop.posts);
			setIsError(false);
			setColumns([
				{
					name: 'ID',
					selector: (row) => row.id,
					sortable: true,
					maxWidth: '30px',
				},
				{
					name: 'Title',
					selector: (row) => row.title,
					sortable: true,
					maxWidth: '70px',
				},
				{
					name: 'Message',
					selector: (row) => row.content,
					sortable: true,
					grow: 1,
				},
				{
					name: 'Created At',
					// We convert the date to a string and split it to get only the date
					selector: (row) => row.created_at.date.toString('').split(' ')[0],
					sortable: true,
					maxWidth: '70px',
				},
				{
					name: 'Author',
					selector: (row) => row.author,
					sortable: true,
					maxWidth: '50px',
				},
				{
					name: 'Forum',
					selector: (row) => row.forum,
					sortable: true,
					maxWidth: '50px',
				},
				{
					name: 'Read',
					cell: (row) => (
						<div>
							<a className='read__button' href={`/post/${row.id}`}>
								Read
							</a>
						</div>
					),
					sortable: false,
					maxWidth: '70px',
				},
				{
					name: 'Modify',
					cell: (row) => (
						<div>
							<a className='edit__button' href={`/post/${row.id}/edit`}>
								Edit
							</a>
						</div>
					),
					sortable: false,
					maxWidth: '70px',
				},
				{
					name: 'Delete',
					cell: (row) => (
						<div>
							<a className='delete__button' href={`/post/${row.id}/delete`}>
								Delete
							</a>
						</div>
					),
					sortable: false,
					maxWidth: '70px',
				},
			]);
		} else if (prop.posts && prop.posts.length > 0 && prop.posts[0].edit === undefined) {
			setDataTableTitle('Posts List');
			setDatas(prop.posts);
			setIsError(false);
			setColumns([
				{
					name: 'ID',
					selector: (row) => row.id,
					sortable: true,
					maxWidth: '30px',
				},
				{
					name: 'Title',
					selector: (row) => row.title,
					sortable: true,
					maxWidth: '70px',
				},
				{
					name: 'Message',
					selector: (row) => row.content,
					sortable: true,
					maxWidth: '500px',
				},
				{
					name: 'Created At',
					// We convert the date to a string and split it to get only the date
					selector: (row) => row.created_at.date.toString('').split(' ')[0],
					sortable: true,
					maxWidth: '70px',
				},
				{
					name: 'Author',
					selector: (row) => row.author,
					sortable: true,
					maxWidth: '50px',
				},
				{
					name: 'Read',
					cell: (row) => (
						<div>
							<a className='read__button' href={`/post/${row.id}`}>
								Read
							</a>
						</div>
					),
					sortable: false,
					maxWidth: '70px',
				},
			]);
		} else {
			setError('No datas to display. Please be creative!!');
			setIsError(true);
		}
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

export default PostsList;
