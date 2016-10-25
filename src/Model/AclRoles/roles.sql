INSERT INTO `_aclRoles` (`id`, `name`, `parentId`, `position`) VALUES
(1,	'guest',	NULL,	1),
(2,	'user',	1,	3),
(3,	'editor',	2,	4),
(4,	'admin',	3,	5),
(5,	'superadmin',	NULL,	2);