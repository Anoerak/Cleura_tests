import React from 'react';

// We create thesaurus of columns for the DataTable component
const columnsSelector = [
	{
		name: 'Users',
		names: ['ID', 'Name', 'Email', 'Roles', 'Edit', 'Delete'],
	},
	{
		name: 'Forums',
		names: ['ID', 'Name', 'Edit', 'Delete'],
	},
	{
		name: 'Posts for Admin',
		names: ['ID', 'Title', 'Message', 'Created_At', 'Author', 'Forum', 'Read', 'Edit', 'Delete'],
	},
	{
		name: 'Posts for Users',
		names: ['ID', 'Title', 'Message', 'Created_At', 'Author', 'Read', 'Edit', 'Delete'],
	},
];

const mappingColumns = (columnName, pathFilter) => {
	const Columns = [];
	// We map the thesaurus of columnSelector for the DataTable component
	columnsSelector
		// We find the column name
		.find((column) => column.name === columnName)
		// We map the column names
		.names.map((name) => {
			// We create columns with buttons if the column name is 'Read', 'Edit' or 'Delete'
			if (name === 'Read' || name === 'Edit' || name === 'Delete') {
				Columns.push({
					name: name,
					cell: (row) => (
						<div>
							<a
								className={
									`${name.toLowerCase()}__button` +
									(() => {
										if (
											name === 'Read' ||
											row.author === row.user_name ||
											row.user_name === 'admin'
										) {
											return '';
										} else {
											return ' disabled';
										}
									})()
								}
								href={(() => {
									switch (name) {
										case 'Read':
											return `/${pathFilter}/${row.id}`;
										default:
											return `/${pathFilter}/${row.id}/${name.toLowerCase()}`;
									}
								})()}
							>
								{name}
							</a>
						</div>
					),
					sortable: false,
					maxWidth: '70px',
				});
			}
			// Otherwise we create columns with datas
			else {
				Columns.push({
					name: name,
					// We lowercase the column name to get the right selector
					// We convert the date to a string and split it to get only the date
					selector: (() => {
						switch (name) {
							case 'Created_At':
								return (row) => row[name.toLowerCase()].date.toString('').split(' ')[0];
							default:
								return (row) => row[name.toLowerCase()];
						}
					})(),
					sortable: true,
					// We use a switch to set the maxWidth of the column
					maxWidth: () => {
						switch (name) {
							case 'ID':
								return '30px';
							case 'Author':
								return '50px';
							case 'Forum':
								return '50px';
							case 'Message':
								return '500px';
							case 'Email':
								return '250px';
							case 'Roles':
								return '250px';
							default:
								return '70px';
						}
					},
				});
			}
		});
	return Columns;
};

// We create a model for the DataTable component which will define the columns and the datas to display
const TableColumnsModel = (prop) => {
	if (prop.users && prop.users.length > 0) {
		const TableTitle = 'Users List';
		const Datas = prop.users;
		const IsError = false;
		const Columns = mappingColumns('Users', 'user');
		return { TableTitle, Datas, IsError, Columns };
	} else if (prop.forums && prop.forums.length > 0) {
		const TableTitle = 'Forums List';
		const Datas = prop.forums;
		const IsError = false;
		const Columns = mappingColumns('Forums', 'forum');
		return { TableTitle, Datas, IsError, Columns };
	} else if (prop.posts && prop.posts.length > 0 && prop.posts[0].edit) {
		const TableTitle = 'Posts List';
		const Datas = prop.posts;
		const IsError = false;
		const Columns = mappingColumns('Posts for Admin', 'post');
		return { TableTitle, Datas, IsError, Columns };
	} else if (prop.posts && prop.posts.length > 0 && prop.posts[0].edit === undefined) {
		const TableTitle = 'Posts List';
		const Datas = prop.posts;
		const IsError = false;
		const Columns = mappingColumns('Posts for Users', 'post');
		return { TableTitle, Datas, IsError, Columns };
	} else {
		const Error = 'No datas to display. Please be creative!!';
		const IsError = true;
		return { Error, IsError };
	}
};

export default TableColumnsModel;
