<?php
 defined("\x49\x4e\x5f\111\101") or die("\101\143\x63\x65\163\x73\x20\x44\x65\156\x69\x65\x64"); class Wn_storexModuleReceiver extends WeModuleReceiver { public function receive() { goto XQf1e; Zw5CH: $openid = trim($this->message["\x66\x72\x6f\x6d\x75\x73\x65\x72\x6e\141\155\145"]); goto eEC4q; U9FFh: kXm7c: goto QHvIC; QHvIC: goto Nn91J; goto L6h1q; jQS1s: pdo_update("\163\x74\157\x72\145\170\137\143\x6f\165\160\x6f\x6e\x5f\162\x65\143\x6f\162\x64", array("\163\x74\x61\x74\x75\x73" => 4), array("\x63\141\162\144\137\x69\x64" => $card_id, "\x6f\x70\145\x6e\x69\144" => $openid, "\143\157\x64\145" => $code)); goto BU03j; XQf1e: global $_W; goto Ju9EO; eLqtj: ntMGA: goto AA2Pv; dfcYX: $code = trim($this->message["\x75\x73\145\x72\x63\141\x72\x64\143\x6f\x64\145"]); goto jQS1s; AA2Pv: $card_id = trim($this->message["\x63\x61\x72\x64\151\x64"]); goto Zw5CH; o83fg: if ($this->message["\x65\166\x65\156\x74"] == "\x75\x73\145\x72\137\147\145\164\x5f\143\x61\162\144") { goto Pzjqo; } goto BSgmg; xAYAb: goto kXm7c; goto X4BV3; FEZGH: pdo_update("\x73\164\x6f\162\145\170\x5f\143\157\165\x70\157\156\x5f\162\145\143\x6f\162\x64", array("\163\x74\x61\164\x75\x73" => 3), array("\143\x61\162\x64\137\x69\x64" => $card_id, "\157\x70\x65\156\151\144" => $openid, "\143\x6f\144\x65" => $code)); goto M3mAF; tdOSv: $vlOEd = !($pcount < $coupon_info["\x67\145\164\x5f\154\x69\x6d\151\x74"] && $coupon_info["\x71\165\x61\x6e\x74\x69\164\x79"] > 0); goto z5DnG; T6gKD: iVZB3: goto WmPcu; MKFSF: $coupon_record = pdo_get("\163\x74\157\x72\145\170\x5f\x63\157\165\160\x6f\x6e\137\x72\145\x63\157\162\x64", array("\143\x61\x72\x64\x5f\x69\144" => trim($this->message["\143\141\x72\144\x69\144"]), "\157\160\x65\156\x69\x64" => trim($this->message["\146\162\157\155\165\x73\145\x72\156\x61\155\x65"]), "\163\164\x61\164\x75\x73" => 1, "\x63\x6f\144\145" => ''), array("\x69\144")); goto sm1Ii; bXp1S: $openid = trim($this->message["\x66\162\x6f\155\165\x73\145\162\156\141\155\x65"]); goto dfcYX; DX_11: if (empty($this->message["\x69\x73\x67\x69\166\145\142\x79\146\x72\151\x65\156\x64"])) { goto wYXxV; } goto Z7v1g; V_0Y9: goto Nn91J; goto b2xAs; n3kBn: $insert_data = array("\165\x6e\151\141\143\151\x64" => $fans_info["\165\156\x69\141\143\x69\x64"], "\x63\141\162\x64\x5f\x69\144" => $this->message["\143\x61\x72\x64\x69\x64"], "\x6f\x70\x65\x6e\151\x64" => $this->message["\x66\162\157\x6d\165\x73\x65\x72\x6e\x61\155\145"], "\x63\x6f\144\145" => $this->message["\165\163\145\162\143\x61\x72\144\143\x6f\144\x65"], "\x61\x64\144\x74\151\x6d\x65" => TIMESTAMP, "\x73\164\x61\x74\165\x73" => "\61", "\x75\151\144" => $fans_info["\165\x69\144"], "\x67\x72\141\x6e\x74\155\x6f\144\x75\x6c\145" => "\167\x6e\x5f\x73\x74\x6f\x72\145\x78", "\x72\145\155\x61\x72\153" => "\347\x94\xa8\xe6\210\xb7\351\200\x9a\xe8\277\207\346\212\225\346\224\276\346\x89\xab\xe7\240\x81", "\x63\x6f\x75\x70\157\x6e\151\x64" => $coupon_info["\x69\144"], "\147\162\141\156\x74\x74\x79\x70\145" => 2); goto K3Dvg; H5LEY: pdo_update("\163\164\x6f\162\x65\x78\x5f\x63\157\x75\160\157\156\137\162\145\x63\x6f\x72\144", array("\x61\144\144\164\151\155\x65" => TIMESTAMP, "\147\x69\166\145\142\x79\x66\162\x69\145\156\x64" => intval($this->message["\x69\163\x67\x69\x76\145\x62\x79\x66\162\x69\x65\156\x64"]), "\157\160\145\156\x69\x64" => trim($this->message["\146\162\x6f\155\165\x73\x65\162\156\141\155\x65"]), "\143\157\144\145" => trim($this->message["\165\163\145\162\143\x61\162\144\x63\x6f\x64\145"]), "\x73\164\x61\x74\x75\x73" => 1), array("\x69\144" => $old_record["\151\144"])); goto xAYAb; eEC4q: $code = trim($this->message["\x75\x73\145\162\x63\141\162\144\x63\x6f\144\145"]); goto FEZGH; b2xAs: Pzjqo: goto DX_11; znkec: $card_id = trim($this->message["\x63\141\162\x64\x69\144"]); goto bXp1S; Ju9EO: load()->model("\155\143"); goto o83fg; qejNF: if ($this->message["\x65\166\x65\x6e\164"] == "\x75\x73\x65\x72\137\x63\157\x6e\x73\165\155\145\137\x63\141\162\x64") { goto ntMGA; } goto V_0Y9; BSgmg: if ($this->message["\x65\166\x65\156\164"] == "\x75\163\x65\162\x5f\x64\x65\154\137\x63\x61\162\144") { goto X729D; } goto qejNF; rkuXN: $fans_info = mc_fansinfo($this->message["\x66\x72\157\155\165\163\x65\162\x6e\x61\x6d\145"]); goto qfVa_; L6h1q: X729D: goto znkec; K3Dvg: pdo_insert("\163\164\x6f\162\x65\x78\137\x63\157\165\x70\157\156\137\162\x65\143\157\162\x64", $insert_data); goto P58bD; BU03j: goto Nn91J; goto eLqtj; sm1Ii: if (!empty($coupon_record)) { goto Fb43p; } goto rkuXN; kMdzT: $pcount = pdo_fetchcolumn("\x53\105\x4c\105\103\x54\x20\103\117\125\x4e\x54\50\x2a\x29\40\106\x52\x4f\x4d\x20" . tablename("\x73\x74\x6f\162\145\x78\x5f\x63\157\x75\x70\x6f\156\x5f\162\145\x63\x6f\x72\144") . "\40\x57\x48\x45\x52\105\40\x60\x6f\160\145\x6e\x69\144\x60\x20\75\40\x3a\157\160\x65\x6e\151\x64\x20\101\116\104\x20\x60\x63\x6f\165\160\157\156\151\144\x60\40\x3d\40\x3a\x63\x6f\x75\160\x6f\x6e\x69\x64", array("\x3a\143\157\165\x70\x6f\x6e\151\x64" => $coupon_info["\x69\144"], "\x3a\x6f\160\x65\x6e\x69\144" => trim($this->message["\x66\162\x6f\155\x75\x73\145\x72\156\141\x6d\x65"]))); goto tdOSv; X4BV3: wYXxV: goto MKFSF; M3mAF: Nn91J: goto fNCwo; P58bD: pdo_update("\x73\x74\157\x72\x65\170\x5f\143\x6f\165\160\x6f\x6e", array("\161\x75\141\156\164\151\x74\x79" => $coupon_info["\x71\x75\141\x6e\164\x69\x74\171"] - 1, "\144\157\x73\141\147\x65" => $coupon_info["\x64\x6f\163\141\x67\x65"] + 1), array("\x75\x6e\x69\141\143\x69\144" => $fans_info["\x75\156\x69\141\x63\151\144"], "\151\144" => $coupon_info["\151\144"])); goto T6gKD; WmPcu: goto paDy2; goto Na9eZ; Na9eZ: Fb43p: goto a6CAK; Z7v1g: $old_record = pdo_get("\163\x74\x6f\x72\x65\170\x5f\x63\157\165\x70\157\156\137\x72\145\143\x6f\162\x64", array("\x6f\x70\x65\156\151\144" => trim($this->message["\x66\x72\151\x65\156\x64\x75\x73\x65\x72\x6e\x61\155\x65"]), "\143\x61\162\x64\x5f\x69\144" => trim($this->message["\x63\x61\162\x64\x69\x64"]), "\143\x6f\x64\x65" => trim($this->message["\x6f\x6c\144\165\163\145\162\x63\141\x72\x64\143\x6f\144\145"]))); goto H5LEY; z5DnG: if ($vlOEd) { goto iVZB3; } goto n3kBn; a6CAK: pdo_update("\x73\x74\157\162\x65\x78\x5f\x63\x6f\165\160\x6f\156\137\162\x65\143\x6f\x72\x64", array("\143\x6f\144\x65" => trim($this->message["\x75\x73\x65\x72\143\x61\x72\x64\x63\x6f\x64\x65"])), array("\x69\144" => $coupon_record["\151\x64"])); goto txq74; qfVa_: $coupon_info = pdo_get("\x73\x74\157\162\x65\x78\x5f\143\x6f\x75\160\157\x6e", array("\x63\141\162\144\x5f\151\144" => $this->message["\143\141\x72\x64\x69\144"])); goto kMdzT; txq74: paDy2: goto U9FFh; fNCwo: } }