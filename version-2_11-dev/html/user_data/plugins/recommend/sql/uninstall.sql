DELETE FROM dtb_blocposition WHERE bloc_id = (SELECT bloc_id FROM dtb_bloc WHERE filename = 'recommend');
DELETE FROM dtb_bloc WHERE filename = 'recommend';
