SELECT *
FROM person
WHERE gender = 2
AND level = (
	SELECT level
	FROM person
	WHERE name = 'Farah'
	LIMIT 1
) - 1;
