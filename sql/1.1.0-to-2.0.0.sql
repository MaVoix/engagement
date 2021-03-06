--
-- Structure de la table `pledge`
--

CREATE TABLE `pledge` (
  `id` int(11) NOT NULL,
  `date_created` datetime DEFAULT NULL,
  `date_amended` datetime DEFAULT NULL,
  `date_deleted` datetime DEFAULT NULL,
  `date_completed` datetime DEFAULT NULL,
  `civility` varchar(4) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `firstname` varchar(255) DEFAULT NULL,
  `ad1` varchar(255) NOT NULL,
  `ad2` varchar(255) NOT NULL,
  `ad3` varchar(255) NOT NULL,
  `city` varchar(255) NOT NULL,
  `zipcode` varchar(10) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `tel` varchar(20) DEFAULT NULL,
  `amount` double DEFAULT NULL,
  `key_edit` int(11) DEFAULT NULL,
  `group_id` int(11) NOT NULL,
  `reference` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Index pour les tables exportées
--

--
-- Index pour la table `pledge`
--
ALTER TABLE `pledge`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT pour les tables exportées
--

--
-- AUTO_INCREMENT pour la table `pledge`
--
ALTER TABLE `pledge`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;


ALTER TABLE `group` ADD `iban` VARCHAR(255) NULL AFTER `bank_city`;
ALTER TABLE `group` ADD `bic` VARCHAR(25) NULL AFTER `iban`;
ALTER TABLE `group` ADD `amount_target` INT NULL AFTER `professions_de_foi`;


CREATE TABLE `transaction` (
  `id` int(11) NOT NULL,
  `date_created` datetime DEFAULT NULL,
  `date_amended` datetime DEFAULT NULL,
  `date_deleted` datetime DEFAULT NULL,
  `reference` text NOT NULL,
  `group_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL COMMENT 'auteur de l insert en base',
  `amount` double DEFAULT NULL,
  `pledge_id` int(11) DEFAULT NULL,
  `path_file` varchar(255) DEFAULT NULL COMMENT 'chemin vers le justificatif',
  `income` double DEFAULT NULL,
  `expense` double DEFAULT NULL,
  `comment` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Index pour les tables exportées
--

--
-- Index pour la table `transaction`
--
ALTER TABLE `transaction`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT pour les tables exportées
--

--
-- AUTO_INCREMENT pour la table `transaction`
--
ALTER TABLE `transaction`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;