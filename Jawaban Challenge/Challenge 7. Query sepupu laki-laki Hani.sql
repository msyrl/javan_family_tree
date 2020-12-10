SELECT *
FROM person
WHERE gender = 1
AND level = (
	SELECT level
	FROM person
	WHERE name = 'Hani'
	LIMIT 1
)
AND parent_id != (
	SELECT parent_id
	FROM person
	WHERE name = 'Hani'
	LIMIT 1
);
