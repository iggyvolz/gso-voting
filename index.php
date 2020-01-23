<!doctype html>
<html>
    <head>
        <title>GSO Voting</title>
        <script src="Sortable.min.js"></script>
        <script>
        window.addEventListener("load", ()=>{
            const list=document.querySelector(".sortable");
            const updateQuery=()=>{
                document.querySelector("textarea").value=JSON.stringify(Array.from(document.querySelector(".sortable").children).map(x=>x.innerText));
            };
            window.s=new Sortable(list);
            updateQuery();
            list.addEventListener("change", updateQuery);
        });
        </script>
        <style>
            textarea {
                width: 100%;
                height: 25%;
            }
        </style>
    </head>
    <body>
        <?php
            // Check if current user is on the list of voters
            // Obtain current voter from Shibboleth
            $voter=$_SERVER["uid"];
            flock($lock=fopen(__DIR__."/data/lock", "r+"), LOCK_SH);
            $voters=array_map("trim", file(__DIR__."/data/voters"));
            // Find the current voter in the list of voters
            $voter_key=array_search($voter, $voters);
            if($voter_key === false) {
                http_response_code(403);
                echo "<p>The user '".htmlspecialchars($voter)."' is not on the list of valid voters.</p></body></html>";
                die();
            }
        ?>
        <p>Hello <?= htmlspecialchars($voter) ?>!</p>
        <form action="run.php" method="post">
            <textarea name="content" readonly></textarea>
            <input type="submit">
        </form>
        <ol class="list-group sortable">
            <li class="list-group-item">Person 1</li>
            <li class="list-group-item">Person 2</li>
            <li class="list-group-item">Person 3</li>
            <li class="list-group-item">Person 4</li>
            <li class="list-group-item">Person 5</li>
        </ol>
        <p>
            Drag the candidates above until they are in an order you would like.  Then press the Submit button above to submit your vote.  Your vote cannot be changed once submitted.
        </p>
    </body>
</html>