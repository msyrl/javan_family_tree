SELECT *
FROM person
WHERE parent_id
IN (
	SELECT id FROM person WHERE parent_id = 1
)
AND gender = 2;
