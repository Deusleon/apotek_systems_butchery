USE apotek_systems_dbms;

ALTER TABLE import_history 
    MODIFY completed_at timestamp NULL,
    MODIFY started_at timestamp NULL,
    MODIFY processing_time decimal(10,2) NULL,
    MODIFY final_summary text NULL; 