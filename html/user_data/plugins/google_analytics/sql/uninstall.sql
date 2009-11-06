DELETE FROM dtb_bloc WHERE bloc_name = 'Google Analytics';
DELETE FROM dtb_blocposition WHERE bloc_id = (SELECT bloc_id FROM dtb_bloc WHERE bloc_name = 'Google Analytics');
