ALTER TABLE tbl_records
  ADD client_immunisation_id int(5) NOT NULL
    AFTER outcome_review;

CREATE TABLE IF NOT EXISTS `tbl_client_immunisation` (
  `ID` int(5) NOT NULL AUTO_INCREMENT,
  `type` varchar(120) NOT NULL,
  `client_ID` int(5) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

INSERT INTO `tbl_notifications` (`ID`, `label`, `value`) VALUES
(11, 'immunisation_schedule', 'daily'),
(12, 'immunisation_weekly', 'saturday'),
(13, 'im_Central', ''),
(14, 'im_Eastern_Highlands', ''),
(15, 'im_Hela', ''),
(16, 'im_Jiwaka', ''),
(17, 'im_Morobe', ''),
(18, 'im_NCD', ''),
(19, 'im_Western_Highlands', ''),
(20, 'im_All_Provinces', 'caroline@susumamas.org.pg,joyce@susumamas.org.pg,robert@kuakawa.biz,mrthemetribe@gmail.com');
COMMIT;

