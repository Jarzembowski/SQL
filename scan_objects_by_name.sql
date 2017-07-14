DECLARE @objectName varchar(MAX)
SET @objectName = 'object_name' WITH SCANOBJECTS AS
  (SELECT 1 AS RESULT,
          obj.name AS [name],
          obj.[type] AS [type],
          T.name AS [table],
          CHARINDEX(CHAR(13),m.[definition]) idx2,
          m.[definition],
          SUBSTRING(m.[definition],0,COALESCE(NULLIF(CHARINDEX(CHAR(13),m.[definition]),0),LEN(m.[definition])+1)) AS str_result
   FROM sys.sql_modules m
   INNER JOIN sys.objects obj ON obj.object_id = m.object_id
   LEFT OUTER JOIN sys.objects T ON T.object_id = obj.parent_object_id
   AND T.type = 'U'
   WHERE m.[definition] LIKE '%'+@objectName+'%'
     UNION ALL
     SELECT SCANOBJECTS.result + 1 AS RESULT ,
            SCANOBJECTS.[name] ,
            SCANOBJECTS.[type] ,
            SCANOBJECTS.[table] ,
            V.x2 AS idx2 ,
            SCANOBJECTS.[definition] ,
            SUBSTRING(SCANOBJECTS.[definition],V.x1,COALESCE(NULLIF(V.x2,0),LEN(SCANOBJECTS.[definition])+1)-V.x1)
     FROM SCANOBJECTS OUTER apply
       (SELECT idx2+1 AS x1 ,CHARINDEX(CHAR(13),SCANOBJECTS.definition,idx2+1) AS x2 )V WHERE idx2>0 )
SELECT SCANOBJECTS.[name] ,
       SCANOBJECTS.[type] ,
       SCANOBJECTS.[table] ,
       SCANOBJECTS.result ,
       SCANOBJECTS.str_result
FROM SCANOBJECTS
WHERE SCANOBJECTS.str_result LIKE '%'+@objectName+'%'
ORDER BY SCANOBJECTS.[name],
         SCANOBJECTS.result OPTION (MAXRECURSION 0);